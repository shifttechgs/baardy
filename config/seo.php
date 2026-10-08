<?php

/*
|--------------------------------------------------------------------------
| Search and AI-answer settings
|--------------------------------------------------------------------------
|
| `reviewed` is the date the loan pages' facts were last checked against the
| client's information. It is shown on each loan page ("Last reviewed") and
| is a freshness signal for search and AI engines, so update it only when the
| copy has genuinely been re-checked.
|
| `ai_crawlers` are the AI search and assistant bots robots.txt names
| explicitly. Allowing them is what lets assistants such as ChatGPT,
| Perplexity, Claude and Gemini read and cite the site. Training-only bots
| (CCBot, anthropic-ai, cohere-ai and the like) are a policy choice for the
| client and are not listed.
*/

return [

    'reviewed' => '2026-10-04',

    'ai_crawlers' => [
        'GPTBot',
        'OAI-SearchBot',
        'ChatGPT-User',
        'PerplexityBot',
        'Perplexity-User',
        'ClaudeBot',
        'Claude-User',
        'Claude-SearchBot',
        'Google-Extended',
        'Applebot-Extended',
        'Bingbot',
    ],

];
