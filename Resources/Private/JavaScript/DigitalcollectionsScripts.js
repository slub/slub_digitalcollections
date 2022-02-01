/*
 *
 * JS functions
 * ================================================
 * a few additional calls to
 * enhance the user experience
 *
 */

window.DigitalCollections = (function (dc) {
    dc.isInTheaterMode = function () {
        return $('body.fullscreen').length > 0;
    };

    dc.toggleTheaterMode = function (persist) {
        if (dc.isInTheaterMode()) {
            exitFullscreen(persist);
        } else {
            enterFullscreen(persist);
        }
    }

    return dc;
})(window.DigitalCollections || {});

$(function () {

    // inital javascript "hello, i'm here!"
    $('html').removeClass('no-js');

    // menu toggles for offcanvas toc and metadata
    $('.offcanvas-toggle').on('click', function (event) {
        $(this).parent().toggleClass('open');
    });

    // active toggle for submenus
    $('li.submenu > a').on('click', function (event) {
        $('li.submenu.open a').not(this).parent().removeClass('open');
        $(this).parent().toggleClass('open');
    });

    // secondary nav toggle
    $('nav .nav-toggle').on('click', function (event) {
        $(this).toggleClass('active');
        $('nav .secondary-nav').toggleClass('open');
    });

    // calendar dropdowns on click/touch
    $('.calendar-view div.issues h4').on('click', function (event) {
        var issuelinks = $(this).parents('div.issues').find('div ul li a');
        if (issuelinks.length == 1) {
            // if only one issue, open this directly
            window.open(issuelinks[0].href, '_self');
        } else {
            $('.calendar-view table td.open').not($(this).parents('td')).removeClass('open');
            $(this).parents('td').toggleClass('open');
        }
    });

    // add body class if any calendar is present
    $('.tx-dlf-calendar, .tx-dlf-calendar-years').parents('body').addClass('calendar');

    // add body class if gridview is shown
    $('.tx-dlf-pagegrid-list').parents('body').addClass('gridview');

    // Inject view switch functions for calendar/list view (initial show calendar)
    if ($('.tx-dlf-calendar .calendar-list-selection a.select-calendar-view').hasClass('active')) {
        $('.tx-dlf-calendar .calendar-list-selection a.select-calendar-view').removeClass('active');
    }
    $('.tx-dlf-calendar .calendar-list-selection a.select-calendar-view, .tx-dlf-calendar .calendar-view').addClass('active');
    $('.tx-dlf-calendar .calendar-list-selection a').on('click', function (event) {
        if (!$(this).hasClass('active')) {
            var targetElement = '.' + $(this).attr('class').replace('select-', '');
            $('.tx-dlf-calendar .active').removeClass('active');
            $(this).addClass('active');
            $(targetElement).addClass('active');
        }
    });

    if ($('.provider img').length > 0) {
        // Avoid broken image display if METS definitions are wrong
        $('.provider img').each(function() {
            if((typeof this.naturalWidth != "undefined" && this.naturalWidth == 0 ) || this.readyState == 'uninitialized' ) {
                $(this).parents('.document-functions').addClass('missing-provider-image');
            }
        });
    } else {
        $('.provider').parents('.document-functions').addClass('missing-provider-image');
    }

    // Copy selected page number to mobile meta (in order to transform select field to ui button)
    if ($('.pages select option[selected]')[0]) {
        $('dl.mobile-meta').append('<dt class="mobile-page-number">No.</dt><dd class="mobile-page-number">' + $('.pages select option[selected]').text() + '</dd>');
    }

    $('.provider').append('<div class="mobile-controls" />');
    $('.view-functions .pages form, .view-functions .zoom a.fullscreen, .fulltext-search-toggle').clone().appendTo('.provider .mobile-controls');

    // Shorten mobile meta title
    shortenMobileMetaElement = $('.provider dl.mobile-meta dd.tx-dlf-title a');
    shortenMobileMetaTitle = shortenMobileMetaElement.text();
    if (shortenMobileMetaTitle.length > 140) {
        shortenMobileMetaTitle = shortenMobileMetaTitle.substr(0, 140) + '...';
        shortenMobileMetaElement.text(shortenMobileMetaTitle);
    }

    // Check if there are is a download list. Otherwise change a to span to disable button
    if (!$('.submenu.downloads ul li')[0]) {
        $(".submenu.downloads>a").replaceWith(function () {
            return $("<span title=\"" + $(this).attr('title') + "\" class=\"" + $(this).attr('class') + "\" id=\"" + $(this).attr('id') + "\">" + $(this).html() + "</span>");
        });
    }

    // Toggle function for full meta data view
    if ($('.tx-dlf-metadata dl.tx-dlf-metadata-titledata').length > 1) {
        metadataToggleLabelMore = ($('html[lang^="de"]')[0]) ? 'mehr Metadaten' : 'more Metadata';
        metadataToggleLabelLess = ($('html[lang^="de"]')[0]) ? 'weniger Metadaten' : 'less Metadata';
        $('.control-bar .metadata-wrapper').append('<div class="metadata-toggle">' + metadataToggleLabelMore + '</div>');
        $('.metadata-toggle').on('click', function () {
            if (!$('.control-bar').hasClass('all-metadata')) {
                Cookies.set('tx-dlf-allmetadata', 'true', { sameSite: 'lax' });
                $(this).text(metadataToggleLabelLess);
            } else {
                Cookies.remove('tx-dlf-allmetadata');
                $(this).text(metadataToggleLabelMore);
            }
            $('.control-bar').toggleClass('all-metadata').find('dl:nth-child(n+3)').slideToggle();
        });
    }

    // enable click on fullscreen button
    $('a.fullscreen').on('click', function () {
        window.DigitalCollections.toggleTheaterMode();
    });

    // if cookie for fullscreen view is present adapat initial page rendering
    if (Cookies.get('tx-dlf-pageview-zoomFullscreen')) {
        $('body').addClass('fullscreen static');
        $('.zoom .fullscreen').addClass('active');
    }

    // TOC folding function to make sure that active pages are in viewport
    if ($('ul.toc ul li.current')[0]) {
        tocPlaceholderLabel = ($('html[lang^="de"]')[0]) ? 'Einige Einträge sind ausgeblendet' : 'Some entires are hidden';
        tocPlaceholderTitle = ($('html[lang^="de"]')[0]) ? 'Hier klicken um alle Einträge zu ziegen' : 'Click to show all entries';
        $('ul.toc ul li.current').first().prevAll(':eq(4)').prevUntil(':nth-child(2)').hide();
        $('ul.toc ul li:nth-child(2)').after('<li class="placeholder" title="' + tocPlaceholderTitle + '">' + tocPlaceholderLabel + '</li>');
        $('ul.toc ul li.placeholder').on('click', function () {
            $(this).remove();
            $('ul.toc ul li').slideDown();
        });
    }

    // Toggle and setup for the 'in document search'
    if ($('.tx-dlf-toolsFulltextsearch form')[0]) {
        $('.fulltext-search-toggle').on('click', function () { // selector should be semantically: .search-indocument-toggle
            $('body').toggleClass('search-indocument-active');
            $('.tx-dlf-toolsFulltextsearch').css({top: ($(this).offset().top - 60) + 'px'});
            $('body.search-indocument-active #tx-dlf-search-in-document-query').trigger('focus');
        });
    } else {
        $('.fulltext-search-toggle').addClass('disabled');
    }

    // Complex page turning mechanism and check if a click on page control was made and unfold next/back navigation
    if (Modernizr.touchevents) {
        $('.fwds, .backs')
            .on('touchstart', function () {
                $(this).addClass('over');
                triggeredElement = $(this);
                setTimeout(function () {
                    triggeredElement.addClass('enable-touchevent');
                }, 250);
            })
            .on('touchend', function () {
                localStorage.txDlfFromPage = $(this).attr('class').split(' ')[0];
            });
        $('body').on('touchstart', function (event) {
            target = $(event.target);
            if (!target.closest('.page-control')[0]) {
                $('.fwds, .backs').removeClass('over enable-touchevent');
                localStorage.clear();
            }
        });
        if (localStorage.txDlfFromPage) {
            $('.' + localStorage.txDlfFromPage).addClass('no-transition over enable-touchevent');
            localStorage.clear();
        }
    } else {
        $('.fwds, .backs')
            .on('mouseenter', function () {
                $(this).addClass('over');
            })
            .on('mouseleave', function () {
                $(this).removeClass('over');
            })
            .on('click', function () {
                localStorage.txDlfFromPage = $(this).attr('class').split(' ')[0];
            });
        if (localStorage.txDlfFromPage) {
            $('.' + localStorage.txDlfFromPage).addClass('no-transition over');
            localStorage.clear();
        }
    }

    // Add a error message if no map element in document viewer given
    if (!$('.tx-dlf-pageview').children()[0]) {
        emptyMessage = ($('html[lang^="de"]')[0]) ? 'Kein Band ausgew&auml;hlt. Klicken Sie hier um zum ersten Band dieses Werks zu gelangen.' : 'No volume selected. Click to jump to the first available volume.';
        $('.tx-dlf-pageview').append('<div class="tx-dlf-empty"><a class="tx-dlf-emptyToFirstVol" href="' + $('.tx-dlf-toc ul li ul li:first-child a').attr('href') + '"><span class="error-arrow">&larr;</span>' + emptyMessage + '</a></div>');
    }

    // Add class to  collection related DD elements in metadata lists
    $('dl.tx-dlf-metadata-titledata').find('dt:contains(mmlung), dt:contains(llection)').nextUntil('dt', 'dd').addClass('tx-dlf-metadata-collection');

    // Finally all things are settled. Bring back animations a second later.
    setTimeout(function () {
        localStorage.clear();
        $('.fwds, .backs').removeClass('no-transition');
        $('body').removeClass('static');
    }, 1000);

});

$(document).keyup(function (e) {

    // Check if ESC key is pressed. Then end fullscreen mode or close SRU form.
    if (e.key === 'Escape') {
        if (window.DigitalCollections.isInTheaterMode()) {
            return exitFullscreen();
        }
        if ($('.document-functions .search.open')[0]) {
            $('.document-functions .search').removeClass('open');
        }
    }
    // Check if the F key is pressed and no text input in SRU form is taking place.
    if (e.key === 'f' && !$('#tx-dlf-search-in-document-query').is(':focus')) {
        return enterFullscreen();
    }

});

// Activate fullscreen mode and set corresponding cookie
function enterFullscreen(persist) {
    persist = persist === undefined ? true : persist;

    setTimeout(function () {
        window.dispatchEvent(new Event('resize'));
    }, 220);
    $("body").addClass('fullscreen');
    $('a.fullscreen').addClass('active');

    Cookies.set('tx-dlf-pageview-zoomFullscreen', 'true', { sameSite: 'lax' });
}

// Exit fullscreen mode and drop cookie
function exitFullscreen(persist) {
    persist = persist === undefined ? true : persist;

    setTimeout(function () {
        window.dispatchEvent(new Event('resize'));
    }, 220);
    $("body").removeClass('fullscreen');
    $('a.fullscreen').removeClass('active');

    if (persist) {
        Cookies.remove('tx-dlf-pageview-zoomFullscreen');
    }
}

// This event is so that the video player, which uses "F" as keybinding to
// toggle fullscreen, can request theater mode via keybinding "T" without
// knowing about how this is implemented.
// See https://github.com/slub/slub_web_sachsendigital/pull/14
window.addEventListener('dlf-theater-mode', (e) => {
    if (e.detail === undefined) {
        console.warn("dlf-theater-mode: No parameter given");
        return;
    }

    switch (e.detail.action) {
        case 'toggle':
            window.DigitalCollections.toggleTheaterMode(e.detail.persist);
            break;
    }
});
