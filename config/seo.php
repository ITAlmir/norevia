<?php

return [
    // Koje page_type vrijednosti hoćeš da indexiraš u sitemap pod /pages/
    // npr. news, landing, legal...
    'indexable_page_types' => [
        'news',
    ],

    // Query-string parametri koji ne smiju biti indexirani (npr. search)
    'noindex_query_params' => [
        'search',
    ],
];
