<?php
function renderArticle($artType, $isLoggedIn)
{
    return "
    <article class='bg-gray-800/50 rounded-xl p-6 hover:bg-gray-800 transition-colors border border-white/5 flex flex-col md:flex-row gap-6 items-start'>
        <div class='w-full md:w-1/3 aspect-video bg-gray-700 rounded-lg flex-shrink-0'></div>
        <div class='flex-1'>
            <div class='flex justify-between items-start mb-2'>
                <span class='text-xs font-semibold text-red-500 uppercase tracking-wider'>Highlight</span>
                <?php if ($isLoggedIn): ?>
                    <button class='text-gray-500 hover:text-red-500 transition-colors' title='Save to profile'>
                        <svg class='w-4 h-4 fill-current' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 384 512'>
                            <path
                                d='M0 482.47V48c0-26.51 21.49-48 48-48h288c26.51 0 48 21.49 48 48v434.47c0 23.36-24.81 37.74-44.5 25.1L192 396.93 44.5 507.57C24.81 520.21 0 505.83 0 482.47z' />
                        </svg>
                    </button>
                <?php endif; ?>
            </div>
            <h3 class='text-xl font-bold mb-3'>The Evolution of
                <?= htmlspecialchars($artType) ?>
                Techniques
            </h3>
            <p class='text-gray-400 mb-4 text-sm'>Explore how styles have shifted and adapted over the
                centuries, influenced by cultural movements and technological advancements.</p>
            <a href='#' class='text-red-400 hover:text-red-300 font-medium inline-flex items-center gap-1 text-sm'>
                Read Article &rarr;
            </a>
        </div>
    </article>";
}