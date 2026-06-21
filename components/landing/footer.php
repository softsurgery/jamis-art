<?php

function landingFooterLayout($path = ".")
{
    return "
    <footer class='relative pt-20 pb-10 overflow-hidden border-t border-white/10'>
        <div class='absolute inset-0 bg-black/50 backdrop-blur-md z-0 pointer-events-none'></div>
        <div class='absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-red-600/10 rounded-full blur-[120px] pointer-events-none'></div>
        
        <div class='max-w-7xl mx-auto px-6 relative z-10'>
            <div class='grid grid-cols-1 md:grid-cols-3 gap-12 mb-16'>
                
                <!-- Column 1: Brand -->
                <div class='flex flex-col items-center md:items-start'>
                    <img src='$path/assets/img/jemis-art-dark.png' alt='JemisArt Logo' class='w-40 h-auto mb-6'>
                    <p class='text-gray-400 text-sm leading-relaxed max-w-sm text-center md:text-left'>
                        JemisArt is more than an art space; it is a movement. Join the House of Resistance and discover your creative identity through dance, music, acting, and painting.
                    </p>
                </div>
                
                <!-- Column 2: Contact -->
                <div class='flex flex-col items-center md:items-start'>
                    <h4 class='text-white font-semibold uppercase tracking-[4px] mb-6'>Contact Us</h4>
                    <ul class='space-y-4 text-gray-400 text-sm'>
                        <li>
                            <a href='mailto:jemisart@gmail.com' class='flex items-center gap-3 hover:text-red-500 transition'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                  <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' />
                                </svg>
                                jemisart@gmail.com
                            </a>
                        </li>
                        <li>
                            <a href='tel:+21629868598' class='flex items-center gap-3 hover:text-red-500 transition'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                  <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z' />
                                </svg>
                                +216 29 868 598
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Column 3: Socials -->
                <div class='flex flex-col items-center md:items-start'>
                    <h4 class='text-white font-semibold uppercase tracking-[4px] mb-6'>Follow The Resistance</h4>
                    <div class='flex gap-4'>
                        <!-- Instagram -->
                        <a href='https://instagram.com/jemisart' target='_blank' rel='noopener noreferrer' class='w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-gradient-to-tr hover:from-[#f09433] hover:via-[#e6683c] hover:to-[#bc1888] hover:border-transparent transition-all duration-300 group'>
                            <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-gray-400 group-hover:text-white transition' fill='currentColor' viewBox='0 0 24 24'>
                              <path d='M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.203 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z'/>
                            </svg>
                        </a>
                        
                        <!-- TikTok -->
                        <a href='https://tiktok.com/@jemisart' target='_blank' rel='noopener noreferrer' class='w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-[#00f2fe]/20 hover:border-[#00f2fe] transition-all duration-300 group'>
                            <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-gray-400 group-hover:text-white transition' fill='currentColor' viewBox='0 0 24 24'>
                              <path d='M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.12-3.44-3.17-3.61-5.46-.02-.43-.02-.86.01-1.29.34-2.19 1.63-4.14 3.51-5.18 1.25-.69 2.72-1.02 4.16-.94v4.03c-1.3-.06-2.61.32-3.58 1.15-1.02.83-1.57 2.1-1.46 3.4.15 1.57 1.43 2.87 2.98 3.1 1.43.2 2.92-.32 3.86-1.37.93-1.01 1.34-2.39 1.3-3.77.01-4.85-.03-9.7.04-14.55z'/>
                            </svg>
                        </a>
                    </div>
                </div>
                
            </div>
            
            <div class='border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left'>
                <p class='text-gray-500 text-sm'>
                    © " . date('Y') . " ArtVerse — Create. Resist. Express.
                </p>
                <div class='flex gap-6 text-sm text-gray-500'>
                    <a href='#' class='hover:text-white transition'>Privacy Policy</a>
                    <a href='#' class='hover:text-white transition'>Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
    ";
}
