import Alpine from 'alpinejs';

/**
 * Alpine drives interaction state: the mobile navigation panel, the loan
 * calculator's inputs and the eligibility checklist. The FAQ accordion uses
 * native <details> elements so it stays keyboard- and screen-reader-correct
 * without any script.
 *
 * Scroll motion below is deliberately NOT Alpine. The reveal pattern it
 * replaces needs four attributes on every element it touches
 * (`x-data`, `x-intersect.once`, `:class`, plus the marker); a plain
 * IntersectionObserver needs one, `data-reveal`, and adds no dependency.
 */
window.Alpine = Alpine;

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

/**
 * Product rail — the facility panels advance as the section is scrolled.
 *
 * The component's own element is the scroll track: it is several viewports
 * tall, and the panels are pinned inside it, so scrolling through the track
 * steps the open panel from the first facility to the last.
 *
 * Only above `lg`. Below that the track has no extra height and the card grid
 * is showing instead, so `sync` exits early and `active` simply stays put.
 *
 * Clicking a panel still opens it. Hover deliberately does NOT: with scroll
 * driving the selection, moving the pointer across a pinned rail fights the
 * scroll position and flickers.
 */
const RAIL = '5rem';
const RAIL_GAP = '0.5rem';

/**
 * A pinned scroll track.
 *
 * The component's own element is the track: it is several viewports tall, its
 * contents are pinned inside it, and scrolling through it moves a continuous
 * cursor from the first item to the last. Everything a track renders is a pure
 * function of that one number, and that number is a pure function of scroll
 * position -- so the items follow the wheel exactly instead of chasing it.
 *
 * Used by the products rail (the process list once used it too). It was stepping a
 * whole-number index at some point and letting CSS transition between states,
 * which lagged the scroll and queued transitions when you moved fast.
 *
 * Above `lg` only. Below that `sync` exits early and `weight` reports every
 * item fully open, because on a phone the track has no extra height and the
 * content should simply read as a static list.
 */
function scrollTrack(count) {
    return {
        /**
         * Continuous position, 0 .. count - 1. A whole number means that item
         * is fully open; 1.4 means the second is mostly open and the third is
         * starting to.
         */
        fraction: 0,
        count,
        ticking: false,
        /** True while the per-frame loop is running, i.e. the track is on screen. */
        tracking: false,
        desktop: window.matchMedia('(min-width: 64rem)'),
        isDesktop: window.matchMedia('(min-width: 64rem)').matches,

        init() {
            this.sync();

            /*
             * Re-sync whenever the track's own box changes.
             *
             * A component can initialise before layout has settled -- fonts
             * still loading, an ancestor still sizing -- and read a viewport
             * that is not the final one. Everything here is derived from that
             * first read, so if it is wrong the whole track is wrong and
             * nothing re-renders until the reader happens to scroll.
             *
             * ResizeObserver fires once on observe and again on every change,
             * which covers the settle, orientation changes and the `lg`
             * breakpoint in one mechanism. `sync` is called directly rather
             * than through `onScroll`, because that defers to an animation
             * frame and this needs to land even where frames are scarce.
             */
            if ('ResizeObserver' in window) {
                new ResizeObserver(() => this.sync()).observe(this.$root);
            }

            this.desktop.addEventListener('change', () => this.sync());
            this.trackWhileVisible();
        },

        /**
         * While the track is on screen, re-read the scroll position every
         * frame instead of waiting for scroll events.
         *
         * Scroll events are not emitted once per frame. During an inertial
         * fling, or a trackpad glide, they arrive in bursts and the rail moves
         * in the same bursts -- correct positions, uneven motion. Sampling per
         * frame makes the movement continuous.
         *
         * It costs a rect read per frame, and only while the section is
         * actually visible: the loop stops the moment it leaves.
         */
        trackWhileVisible() {
            if (!('IntersectionObserver' in window)) {
                return;
            }

            const frame = () => {
                if (!this.tracking) {
                    return;
                }

                this.sync();
                window.requestAnimationFrame(frame);
            };

            new IntersectionObserver(
                ([entry]) => {
                    if (entry.isIntersecting === this.tracking) {
                        return;
                    }

                    this.tracking = entry.isIntersecting;

                    if (this.tracking) {
                        window.requestAnimationFrame(frame);
                    }
                },
                { rootMargin: '120px' },
            ).observe(this.$root);
        },

        /**
         * Scroll fallback, for when the per-frame loop is not running -- no
         * IntersectionObserver, or the track is off screen and something has
         * jumped the page.
         */
        onScroll() {
            if (this.ticking || this.tracking) {
                return;
            }

            this.ticking = true;

            window.requestAnimationFrame(() => {
                this.ticking = false;
                this.sync();
            });
        },

        /**
         * Distance the track can travel before its base reaches the viewport floor.
         *
         * Every measurement in here uses `$root`, never `$el`. Inside a method
         * called from an event handler on a CHILD (a panel's click), `$el` is
         * that child -- so `open()` measured one panel instead of the track,
         * got a negative distance and silently did nothing. `$root` is always
         * the track.
         */
        travel() {
            return this.$root.getBoundingClientRect().height - window.innerHeight;
        },

        sync() {
            // Mirrored onto reactive state so a resize across the breakpoint
            // re-renders; a media query object read inside a getter does not.
            this.isDesktop = this.desktop.matches;

            if (!this.isDesktop) {
                return;
            }

            const distance = this.travel();

            if (distance <= 0) {
                return;
            }

            const { top } = this.$root.getBoundingClientRect();
            const progress = Math.min(Math.max(-top / distance, 0), 1);

            this.fraction = progress * (this.count - 1);
        },

        /**
         * How open an item is: 1 when it is the one being read, falling
         * linearly to 0 as the neighbour takes over.
         *
         * The weights of any two adjacent items always sum to 1, which is what
         * keeps a row of panels exactly as wide as its track at every point in
         * between.
         */
        weight(index) {
            /*
             * Touch the reactive mirror so a breakpoint change re-renders, but
             * decide on the LIVE media query.
             *
             * Trusting the mirror alone was a real bug: if the component
             * initialised before layout settled, `isDesktop` latched false on a
             * desktop viewport and never corrected until something scrolled.
             * Every panel then reported weight 1, so the products rail rendered
             * four full-width panels side by side and blew the page out
             * horizontally -- roughly 3,500px of content in a 1,425px window.
             */
            void this.isDesktop;

            if (!this.desktop.matches) {
                return 1;
            }

            const linear = Math.max(0, 1 - Math.abs(this.fraction - index));

            /*
             * Eased, not linear. A linear ramp changes at a constant rate and
             * so has a corner at each end -- an item finishes opening at full
             * speed and stops dead, which is exactly the moment the eye is on
             * it. Smoothstep leaves and arrives at zero velocity.
             *
             * It also keeps the invariant the products rail is built on:
             * smoothstep is symmetric about (0.5, 0.5), so f(t) + f(1 - t) is
             * still exactly 1 and two adjacent panels still fill the track.
             */
            return linear * linear * (3 - 2 * linear);
        },

        /**
         * Choosing an item scrolls to the point on the track where it is open,
         * rather than setting state directly. Scroll position stays the single
         * source of truth, so the two ways of choosing cannot disagree, and the
         * browser's own smooth scrolling does the easing -- which respects
         * prefers-reduced-motion for free.
         */
        open(index) {
            const distance = this.travel();

            if (!this.isDesktop || distance <= 0) {
                return;
            }

            const top = this.$root.getBoundingClientRect().top + window.scrollY;
            const target = top + (index / (this.count - 1)) * distance;
            const from = window.scrollY;

            window.scrollTo({ top: target, behavior: 'smooth' });

            /*
             * Some contexts accept a smooth scroll and then never animate it --
             * a background tab is the usual one, since the animation needs
             * frames. The click would silently do nothing. If the page has not
             * begun to move a few frames later, jump instead: a hard cut beats
             * a control that appears to be broken.
             *
             * `behavior: 'auto'` is load-bearing. The stylesheet sets
             * `scroll-behavior: smooth` on <html>, which governs programmatic
             * scrolls too -- so a bare scrollTo(x, y) here would also be
             * smooth, and just as inert as the call it is meant to rescue.
             */
            window.setTimeout(() => {
                if (window.scrollY === from && Math.abs(target - from) > 1) {
                    window.scrollTo({ top: target, behavior: 'auto' });
                }
            }, 150);
        },
    };
}

/** The four facilities, as horizontally expanding panels. */
Alpine.data('productRail', (count) => ({
    ...scrollTrack(count),

    /**
     * Width as a share of the leftover space: a closed rail plus its weight of
     * everything that is not rail or gap. At weight 1 this is the full open
     * width; at 0 it is exactly one rail.
     */
    widthStyle(index) {
        const share = this.weight(index).toFixed(4);

        return `width: calc(${RAIL} + ${share} * (100% - ${RAIL} * ${this.count} - ${RAIL_GAP} * ${this.count - 1}))`;
    },

    /**
     * Vertical name on the closed rail. It holds most of the way through the
     * opening, only clearing at 80%.
     *
     * The two ramps below used to meet exactly at half open, which left one
     * frame where the rail had faded out and the term sheet had not yet begun
     * and the panel was a blank colour field. They now overlap.
     */
    railOpacity(index) {
        return Math.min(Math.max(1 - this.weight(index) * 1.25, 0), 1);
    },

    /**
     * The term sheet. Deliberately late: it only starts at 60% open, by which
     * point the panel is wide enough to set the heading and the figures
     * without them wrapping into a narrow column on the way past.
     */
    contentOpacity(index) {
        return Math.min(Math.max((this.weight(index) - 0.6) * 2.5, 0), 1);
    },

    /**
     * The panel's photograph: blurred, desaturated and slightly zoomed while
     * the panel is a closed rail, sharp and full-colour as it opens. Driven by
     * the same weight as the width, so it never lags the scroll.
     */
    photoStyle(index, position) {
        const w = this.weight(index);

        return `object-position: ${position}; `
            + `filter: blur(${((1 - w) * 6).toFixed(2)}px) saturate(${(0.4 + w * 0.6).toFixed(3)}); `
            + `transform: translateX(-50%) scale(${(1.12 - w * 0.12).toFixed(4)})`;
    },
}));

/**
 * Hero photographs -- rotation, progress and parallax.
 *
 * One rAF loop drives everything, and only while the hero is on screen and
 * the tab is visible:
 *
 *   rotation  `progress` runs 0 -> 1 over `interval` ms, then the next slide
 *             becomes active (the crossfade itself is CSS, on .hero-photo)
 *   parallax  the photograph stack eases toward the pointer and sinks as the
 *             page scrolls, written straight to a transform on $refs.parallax
 *
 * Reduced motion: no rotation, no parallax -- the first photograph, still.
 * The rotation can be paused from the slide control (WCAG 2.2.2), and it
 * holds while the hero is off screen, so returning to it does not land
 * mid-fade on a slide the reader never saw start.
 */
Alpine.data('heroSlides', (count, interval = 7000) => ({
    count,
    interval,
    active: 0,
    progress: 0,
    paused: false,
    still: reducedMotion.matches,
    visible: true,
    elapsed: 0,
    last: 0,
    pointer: { x: 0, y: 0 },
    eased: { x: 0, y: 0 },
    lastTransform: '',

    init() {
        if (this.still) {
            return;
        }

        if (window.matchMedia('(pointer: fine)').matches) {
            window.addEventListener(
                'pointermove',
                (event) => {
                    this.pointer.x = (event.clientX / window.innerWidth) * 2 - 1;
                    this.pointer.y = (event.clientY / window.innerHeight) * 2 - 1;
                },
                { passive: true },
            );
        }

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(([entry]) => {
                this.visible = entry.isIntersecting;
            }).observe(this.$el);
        }

        const frame = (now) => {
            const delta = this.last ? Math.min(now - this.last, 100) : 0;
            this.last = now;

            if (this.visible && !document.hidden) {
                this.tick(delta);
                this.move();
            }

            window.requestAnimationFrame(frame);
        };

        window.requestAnimationFrame(frame);
    },

    tick(delta) {
        if (this.paused || this.count < 2) {
            return;
        }

        this.elapsed += delta;
        this.progress = Math.min(this.elapsed / this.interval, 1);

        if (this.progress >= 1) {
            this.go(this.active + 1);
        }
    },

    /**
     * Ease the stack toward the pointer, and let it sink as the page scrolls.
     *
     * Writes only when the result changes: once the pointer is still and the
     * page is not scrolling, the frame loop costs nothing -- no style write,
     * so no recalculation. (Writing every frame regardless was measurable
     * main-thread work on a phone.)
     */
    move() {
        this.eased.x += (this.pointer.x - this.eased.x) * 0.06;
        this.eased.y += (this.pointer.y - this.eased.y) * 0.06;

        const sink = Math.min(window.scrollY, window.innerHeight) * 0.25;
        const transform = `translate3d(${(-this.eased.x * 18).toFixed(1)}px, ${(-this.eased.y * 12 + sink).toFixed(1)}px, 0)`;

        if (transform !== this.lastTransform) {
            this.$refs.parallax.style.transform = transform;
            this.lastTransform = transform;
        }
    },

    go(index) {
        this.active = (index + this.count) % this.count;
        this.elapsed = 0;
        this.progress = 0;
    },

    toggle() {
        this.paused = !this.paused;
    },

    /** Fill of one segment of the slide control, 0-1. */
    fill(index) {
        if (index < this.active) {
            return 1;
        }

        return index === this.active ? this.progress : 0;
    },
}));

/**
 * The site header: one floating pill that condenses, expands and opens menus.
 *
 *   compact   scrolling DOWN past the hero condenses the pill to the mark and
 *             the call to action; any scroll UP expands it again, wherever
 *             the reader is on the page.
 *   menu      the open desktop menu ('loans', 'about', 'insights' or null).
 *             The pill itself grows downward into the menu panel; `height`
 *             is measured from the open panel's content so the pill animates
 *             between menus of different sizes, not just open and shut.
 *             Opens on hover (with a short intent delay) or on click/Enter;
 *             closes on leaving the header, Escape, or scrolling.
 *   open      the mobile panel, which grows out of the same pill.
 *   overPhoto the homepage hero state: dark glass and light type while the
 *             page is at the top and nothing is open. Rendered on the server
 *             too, so the first paint is already correct.
 */
Alpine.data('siteHeader', (overlay) => ({
    overlay,
    y: window.scrollY,
    lastY: window.scrollY,
    compact: false,
    menu: null,
    open: false,
    height: 0,
    timer: null,

    get overPhoto() {
        return this.overlay && this.y < 8 && !this.menu && !this.open;
    },

    /**
     * Keep the pill's height in step with whichever panel is open: a panel
     * reports a size the moment it is displayed, and again whenever its
     * content settles (images loading, the window resizing).
     */
    init() {
        const observer = new ResizeObserver(() => this.measure());

        ['menu-loans', 'menu-about', 'menu-insights', 'mobile'].forEach((ref) => {
            if (this.$refs[ref]) {
                observer.observe(this.$refs[ref]);
            }
        });
    },

    onScroll() {
        this.y = window.scrollY;
        const delta = this.y - this.lastY;

        if (Math.abs(delta) > 4) {
            this.compact = this.y > 160 && delta > 0;
            this.lastY = this.y;
        }

        if (this.menu && Math.abs(delta) > 4) {
            this.closeMenu(true);
        }
    },

    /**
     * Measure the open panel so the pill's height can animate to it. Runs
     * after Alpine has applied x-show, so the panel is already displayed.
     */
    measure() {
        this.$nextTick(() => {
            const panel = this.menu ? this.$refs[`menu-${this.menu}`] : (this.open ? this.$refs.mobile : null);
            this.height = panel ? panel.offsetHeight : 0;
        });
    },

    openMenu(name, now = false) {
        clearTimeout(this.timer);
        const go = () => {
            this.menu = name;
            this.compact = false;
            this.measure();
        };
        now || this.menu ? go() : (this.timer = setTimeout(go, 90));
    },

    closeMenu(now = false) {
        clearTimeout(this.timer);
        const go = () => {
            this.menu = null;
            this.measure();
        };
        now ? go() : (this.timer = setTimeout(go, 160));
    },

    toggleMenu(name) {
        this.menu === name ? this.closeMenu(true) : this.openMenu(name, true);
    },

    toggleMobile() {
        this.open = !this.open;
        this.compact = false;
        this.measure();
    },

    escape() {
        if (this.menu) {
            const trigger = this.$refs[`trigger-${this.menu}`];
            this.closeMenu(true);
            trigger?.focus();
        } else if (this.open) {
            this.toggleMobile();
            this.$refs.toggle.focus();
        }
    },
}));

/**
 * The closing call to action's application slip.
 *
 * The slip is a plain GET form to /#contact, so without script (or on a page
 * with no enquiry form, such as /insights) it lands on the homepage form with
 * ?interest=&branch=, which the form reads to preselect both answers.
 *
 * Where the enquiry form is on the same page, this skips the reload: it
 * copies the two answers into the form, scrolls to it and puts the cursor in
 * the name field, so the reader carries straight on.
 */
Alpine.data('applicationSlip', (interest, branch) => ({
    interest,
    branch,

    carryOn(event) {
        const form = document.querySelector('#contact form');

        if (!form) {
            return;
        }

        event.preventDefault();

        const select = form.querySelector('[name="interest"]');
        select.value = this.interest;
        select.dispatchEvent(new Event('change', { bubbles: true }));

        const radio = form.querySelector(`[name="branch"][value="${CSS.escape(this.branch)}"]`);

        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        }

        document.getElementById('contact').scrollIntoView({ behavior: reducedMotion.matches ? 'auto' : 'smooth', block: 'start' });

        // Focus once the scroll has settled, without jumping the page again.
        setTimeout(() => form.querySelector('[name="name"]')?.focus({ preventScroll: true }), reducedMotion.matches ? 0 : 700);
    },
}));

/**
 * FAQ: open a question by resting the pointer on it.
 *
 * The rows are native, exclusive <details> (x-ui.accordion-item), so opening
 * one closes the others and clicking still works everywhere. This only adds
 * hover-to-open on fine pointers, with two guards against the classic
 * hover-accordion flicker, where rows collapsing above slide a different row
 * under a still cursor and open it:
 *
 *   intent   a row opens only after the pointer MOVES over it and then rests
 *            for `delay` ms. Layout shifting under a stationary cursor fires
 *            no mousemove, so it cannot open anything.
 *   settle   after a row opens, further hover-opens wait until its height
 *            animation (0.55s in app.css) has finished.
 *
 * Rows never close on mouseleave: the answer stays put while it is read,
 * and closes only when another row opens.
 */
Alpine.data('hoverAccordion', (delay = 180, settle = 600) => ({
    enabled: window.matchMedia('(hover: hover) and (pointer: fine)').matches,
    timer: null,
    lockedUntil: 0,

    intend(event) {
        if (!this.enabled) {
            return;
        }

        const row = event.target.closest('details');

        if (!row || row.open) {
            this.cancel();

            return;
        }

        clearTimeout(this.timer);

        const attempt = (wait) => {
            this.timer = setTimeout(() => {
                if (!row.matches(':hover')) {
                    return;
                }

                // Still settling from the last open: try again once it has.
                const remaining = this.lockedUntil - performance.now();

                if (remaining > 0) {
                    attempt(remaining);

                    return;
                }

                row.open = true;
                this.lockedUntil = performance.now() + settle;
            }, wait);
        };

        attempt(delay);
    },

    cancel() {
        clearTimeout(this.timer);
    },
}));

/**
 * The paper stack in "How it works" -- the section's signature moment.
 *
 * Three documents (checklist, appraisal sheet, disbursement advice) sit in a
 * stack; `active` is the one at the front. Everything the stack draws --
 * which sheet leads, the ticks, the stamp, the slip -- reads from `active`.
 *
 *   autoplay  when the section first comes into view, it steps through the
 *             documents once, then hands over to the reader. Where there is
 *             no hover (touch), it keeps cycling until the reader taps a step.
 *   reader    hovering or tapping a step brings its document forward and
 *             stops the autoplay for good.
 *   pointer   on fine pointers the stack leans a few degrees toward the
 *             cursor, written as two custom properties the CSS reads.
 *
 * Reduced motion: no autoplay and no lean -- the first document, still, and
 * a step can still be chosen (the change is instant under the global rule).
 */
Alpine.data('paperStack', (count, interval = 2600) => ({
    count,
    active: 0,
    playing: false,
    touched: false,
    timer: null,
    finePointer: window.matchMedia('(hover: hover) and (pointer: fine)').matches,

    init() {
        if (reducedMotion.matches) {
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !this.touched && !this.playing) {
                        this.play();
                        observer.disconnect();
                    }
                });
            },
            { threshold: 0.45 },
        );

        observer.observe(this.$el);
    },

    play() {
        this.playing = true;
        let stepsLeft = this.count - 1;

        this.timer = setInterval(() => {
            if (document.hidden) {
                return;
            }

            this.active = (this.active + 1) % this.count;
            stepsLeft -= 1;

            // Desktop: one pass, then the reader is in charge.
            if (this.finePointer && stepsLeft <= 0) {
                this.stop();
            }
        }, interval);
    },

    stop() {
        clearInterval(this.timer);
        this.playing = false;
    },

    /** The reader chose a step: bring it forward and stop the autoplay. */
    choose(index) {
        this.touched = true;
        this.stop();
        this.active = index;
    },

    /** Position of document `index` relative to the front: 0 front, then behind. */
    depth(index) {
        return (index - this.active + this.count) % this.count;
    },

    /** Transform, stacking and shadow for one sheet, from its depth. */
    sheetStyle(index) {
        const places = [
            { x: 0, y: 0, rotate: -1.5, scale: 1, z: 30, shadow: 1 },
            { x: 16, y: -9, rotate: 5, scale: 0.9, z: 20, shadow: 0.45 },
            { x: -15, y: -5, rotate: -7, scale: 0.84, z: 10, shadow: 0.25 },
        ];
        const place = places[Math.min(this.depth(index), places.length - 1)];

        return [
            `transform: translate(-50%, -50%) translate(${place.x}%, ${place.y}%) rotate(${place.rotate}deg) scale(${place.scale})`,
            `z-index: ${place.z}`,
            `--sheet-lift: ${place.shadow}`,
        ].join('; ');
    },

    /** Lean toward the pointer (fine pointers only), as -1..1 on each axis. */
    lean(event) {
        if (!this.finePointer || reducedMotion.matches) {
            return;
        }

        const stage = this.$refs.stage;
        const box = stage.getBoundingClientRect();
        stage.style.setProperty('--lean-x', (((event.clientX - box.left) / box.width) * 2 - 1).toFixed(3));
        stage.style.setProperty('--lean-y', (((event.clientY - box.top) / box.height) * 2 - 1).toFixed(3));
    },

    settle() {
        this.$refs.stage.style.setProperty('--lean-x', 0);
        this.$refs.stage.style.setProperty('--lean-y', 0);
    },

    destroy() {
        this.stop();
    },
}));

Alpine.start();

/**
 * Tell the stylesheet that the reveal rules are safe to apply.
 *
 * Every hidden-until-revealed rule is scoped to this attribute, so if the
 * bundle fails to load or JavaScript is off, nothing is ever set to opacity 0
 * and the page reads as plain static content.
 */
function markRevealReady() {
    document.documentElement.setAttribute('data-reveal-ready', '');
}

/**
 * Scroll-triggered reveal.
 *
 * Blocks marked [data-reveal] settle into place as they enter the viewport.
 * `once` is the whole behaviour: each element is unobserved the moment it has
 * been shown, so scrolling back up never replays anything and the observer
 * empties itself as the page is read.
 *
 * [data-reveal-stagger], [data-reveal-cards] and [data-reveal-pop] additionally
 * number their direct children through --i, which the stylesheet turns into a
 * per-child delay so the group arrives one item at a time rather than all at
 * once.
 */
/** Adds `.is-revealed` the first time each element crosses into view, then stops watching it. */
function revealOnce(targets, options) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
        });
    }, options);

    targets.forEach((el) => observer.observe(el));
}

function initReveal() {
    const blocks = document.querySelectorAll(
        '[data-reveal], [data-reveal-stagger], [data-reveal-cards], [data-reveal-pop]',
    );
    const steps = document.querySelectorAll('[data-reveal-step]');
    const all = [...blocks, ...steps];

    if (!all.length) {
        return;
    }

    blocks.forEach((el) => {
        // All three stagger variants read --i off each child; see app.css for
        // why there are three.
        if (
            el.hasAttribute('data-reveal-stagger')
            || el.hasAttribute('data-reveal-cards')
            || el.hasAttribute('data-reveal-pop')
        ) {
            Array.from(el.children).forEach((child, i) => {
                child.style.setProperty('--i', i);
            });
        }
    });

    // Without IntersectionObserver, or with reduced motion, show everything now.
    if (reducedMotion.matches || !('IntersectionObserver' in window)) {
        all.forEach((el) => el.classList.add('is-revealed'));

        return;
    }

    // Blocks settle just before their top edge arrives, so the motion finishes
    // as they reach reading position rather than after.
    revealOnce(blocks, { rootMargin: '0px 0px -12% 0px', threshold: 0.1 });

    /*
     * Steps wait considerably longer -- until their top crosses roughly the
     * lower third of the viewport. A step is meant to open as the reader
     * arrives at it; on the block timing above, all four would be open before
     * the first had been read, which is no sequence at all.
     */
    revealOnce(steps, { rootMargin: '0px 0px -38% 0px', threshold: 0 });
}

/**
 * Word-by-word statement reveal.
 *
 * The text of a [data-word-reveal] element is split into per-word spans that
 * light as the block travels up the viewport, so a sentence is read at the
 * pace it is scrolled rather than arriving all at once.
 *
 * Markup inside is kept: text is split into word spans in place, and
 * elements -- a <br>, a coloured <span> -- are left where they are, with
 * their own text split the same way. So a heading keeps its line breaks and
 * its accent colour while its words light.
 */
function initWordReveal() {
    const targets = document.querySelectorAll('[data-word-reveal]');

    if (!targets.length) {
        return;
    }

    const instances = [];

    /** Replace each text node under `node` with word spans; return the spans in reading order. */
    const split = (node) => {
        const words = [];

        [...node.childNodes].forEach((child) => {
            if (child.nodeType === Node.ELEMENT_NODE) {
                words.push(...split(child));

                return;
            }

            if (child.nodeType !== Node.TEXT_NODE || !child.textContent.trim()) {
                return;
            }

            const fragment = document.createDocumentFragment();
            const parts = child.textContent.split(/(\s+)/);

            parts.forEach((part) => {
                if (!part) {
                    return;
                }

                if (/^\s+$/.test(part)) {
                    fragment.append(document.createTextNode(' '));

                    return;
                }

                const span = document.createElement('span');
                span.className = 'wr-word';
                span.textContent = part;
                fragment.append(span);
                words.push(span);
            });

            child.replaceWith(fragment);
        });

        return words;
    };

    targets.forEach((el) => {
        if (!el.textContent.trim()) {
            return;
        }

        const words = split(el);

        if (reducedMotion.matches) {
            words.forEach((word) => word.classList.add('is-lit'));

            return;
        }

        instances.push({ el, words });
    });

    if (!instances.length) {
        return;
    }

    let ticking = false;

    const update = () => {
        ticking = false;

        const viewportHeight = window.innerHeight;

        instances.forEach(({ el, words }) => {
            const { top } = el.getBoundingClientRect();

            // 0 while the block's top sits at 80% of the viewport height,
            // reaching 1 by the time it has risen to 30%.
            const start = viewportHeight * 0.8;
            const end = viewportHeight * 0.3;
            const progress = Math.max(0, Math.min(1, (start - top) / (start - end)));
            const lit = Math.round(progress * words.length);

            words.forEach((word, i) => word.classList.toggle('is-lit', i < lit));
        });
    };

    const onScroll = () => {
        if (ticking) {
            return;
        }

        ticking = true;
        window.requestAnimationFrame(update);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });

    /*
     * A page opened in a background tab gets no animation frames, so the first
     * update() never runs. Without this the statement would sit dim until the
     * reader happened to scroll it -- which, if it is already in view when they
     * switch to the tab, could be never.
     */
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            onScroll();
        }
    });

    update();
}

/**
 * Count-up figures.
 *
 * An element with [data-count-to] counts from zero to its value, easing out,
 * after [data-count-delay] milliseconds -- timed to start as the card holding
 * it lands. The server renders the final number, so with JavaScript off or
 * reduced motion on, the real figure is simply there.
 */
function initCountUp() {
    const targets = document.querySelectorAll('[data-count-to]');

    if (!targets.length || reducedMotion.matches) {
        return;
    }

    const duration = 1600;
    const easeOutExpo = (t) => (t === 1 ? 1 : 1 - 2 ** (-10 * t));

    const run = (el, delay) => {
        const end = Number(el.dataset.countTo);

        window.setTimeout(() => {
            const start = performance.now();

            const frame = (now) => {
                const progress = Math.min((now - start) / duration, 1);

                el.textContent = String(Math.round(end * easeOutExpo(progress)));

                if (progress < 1) {
                    window.requestAnimationFrame(frame);
                }
            };

            window.requestAnimationFrame(frame);
        }, delay);

        // A background tab gets no frames; never leave the figure at zero.
        window.setTimeout(() => {
            el.textContent = String(end);
        }, delay + duration + 400);
    };

    /*
     * Figures marked [data-count-on-view] wait until they scroll into view,
     * and start on the same stagger as the cell they sit in, so each number
     * begins counting as its cell lands rather than all at once.
     */
    const onView = 'IntersectionObserver' in window
        ? new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    observer.unobserve(entry.target);

                    const cell = entry.target.closest('[data-reveal-stagger] > *');
                    const index = cell ? Number(cell.style.getPropertyValue('--i') || 0) : 0;

                    run(entry.target, 250 + index * 110);
                });
            },
            { rootMargin: '0px 0px -12% 0px', threshold: 0.4 },
        )
        : null;

    targets.forEach((el) => {
        if (!Number.isFinite(Number(el.dataset.countTo))) {
            return;
        }

        el.textContent = '0';

        if (el.hasAttribute('data-count-on-view') && onView) {
            onView.observe(el);

            return;
        }

        run(el, Number(el.dataset.countDelay || 0));
    });
}

function initMotion() {
    markRevealReady();
    initReveal();
    initWordReveal();
    initCountUp();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMotion);
} else {
    initMotion();
}
