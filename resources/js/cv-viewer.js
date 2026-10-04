/*
 * The CV reader on an application's page (admin panel).
 *
 * Draws a PDF with PDF.js onto canvases inside the card, so there is no
 * browser viewer chrome: white sheets on a soft grey, fitted to the card's
 * width, with a page count and zoom. Loaded only on that page. Anything that
 * goes wrong falls back to a plain "open it in a new tab" message.
 */
import * as pdfjs from 'pdfjs-dist';
import workerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjs.GlobalWorkerOptions.workerSrc = workerUrl;

const ZOOM_STEPS = [0.6, 0.8, 1, 1.25, 1.5, 2];

async function mount(root) {
    if (root.dataset.cvReady) {
        return;
    }

    root.dataset.cvReady = '1';

    const scroller = root.querySelector('[data-cv-scroll]');
    const pagesEl = root.querySelector('[data-cv-pages]');
    const status = root.querySelector('[data-cv-status]');
    const pageLabel = root.querySelector('[data-cv-page]');
    const zoomLabel = root.querySelector('[data-cv-zoom-label]');
    const zoomIn = root.querySelector('[data-cv-zoom-in]');
    const zoomOut = root.querySelector('[data-cv-zoom-out]');

    let zoomIndex = ZOOM_STEPS.indexOf(1);
    let pdf = null;
    let drawing = 0;

    const fail = () => {
        status.hidden = false;
        status.textContent = 'This CV could not be shown here. Use View CV to open it in a new tab.';
    };

    async function draw() {
        const run = ++drawing;
        const width = Math.max(scroller.clientWidth - 32, 240);
        const ratio = window.devicePixelRatio || 1;
        const fragment = document.createDocumentFragment();

        for (let number = 1; number <= pdf.numPages; number++) {
            const page = await pdf.getPage(number);

            if (run !== drawing) {
                return;
            }

            const base = page.getViewport({ scale: 1 });
            const scale = (width / base.width) * ZOOM_STEPS[zoomIndex];
            const viewport = page.getViewport({ scale });

            const sheet = document.createElement('div');
            sheet.dataset.cvSheet = String(number);
            sheet.style.cssText = `width:${Math.floor(viewport.width)}px;height:${Math.floor(viewport.height)}px;background:#fff;border-radius:6px;box-shadow:0 1px 2px rgb(16 24 40 / .06),0 8px 24px -12px rgb(16 24 40 / .25);flex:none;overflow:hidden`;

            const canvas = document.createElement('canvas');
            canvas.width = Math.floor(viewport.width * ratio);
            canvas.height = Math.floor(viewport.height * ratio);
            canvas.style.cssText = 'display:block;width:100%;height:100%';
            sheet.appendChild(canvas);
            fragment.appendChild(sheet);

            await page.render({
                canvas,
                viewport: page.getViewport({ scale: scale * ratio }),
            }).promise;
        }

        if (run === drawing) {
            pagesEl.replaceChildren(fragment);
            status.hidden = true;
            updatePage();
        }
    }

    function updatePage() {
        const sheets = [...pagesEl.querySelectorAll('[data-cv-sheet]')];
        const middle = scroller.scrollTop + scroller.clientHeight / 3;
        let current = 1;

        sheets.forEach((sheet, index) => {
            if (sheet.offsetTop <= middle) {
                current = index + 1;
            }
        });

        pageLabel.textContent = `Page ${current} of ${pdf.numPages}`;
    }

    function setZoom(next) {
        zoomIndex = Math.min(Math.max(next, 0), ZOOM_STEPS.length - 1);
        zoomLabel.textContent = `${Math.round(ZOOM_STEPS[zoomIndex] * 100)}%`;
        zoomOut.disabled = zoomIndex === 0;
        zoomIn.disabled = zoomIndex === ZOOM_STEPS.length - 1;
        draw();
    }

    try {
        pdf = await pdfjs.getDocument({ url: root.dataset.src, withCredentials: true }).promise;
    } catch (error) {
        fail();

        return;
    }

    zoomOut.addEventListener('click', () => setZoom(zoomIndex - 1));
    zoomIn.addEventListener('click', () => setZoom(zoomIndex + 1));
    scroller.addEventListener('scroll', updatePage, { passive: true });

    let resizeTimer;
    new ResizeObserver(() => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(draw, 200);
    }).observe(scroller);

    setZoom(zoomIndex);
}

function mountAll() {
    document.querySelectorAll('[data-cv-viewer]').forEach((root) => mount(root).catch(() => {}));
}

document.addEventListener('DOMContentLoaded', mountAll);
document.addEventListener('livewire:navigated', mountAll);
mountAll();
