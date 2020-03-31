<INCLUDE_TYPOSCRIPT: source="FILE:EXT:slub_digitalcollections/Configuration/TypoScript/Plugin/Kitodo/common.ts">

# --------------------------------------------------------------------------------------------------------------------
# search
# --------------------------------------------------------------------------------------------------------------------
plugin.tx_dlf_search {
  templateFile =  {$config.kitodo.templates.search}
}

lib.kitodo.fulltext.search = USER
lib.kitodo.fulltext.search {
    includeLibs = EXT:dlf/plugins/search/class.tx_dlf_search.php
    userFunc = tx_dlf_search->main
    // storagePid of SLUB Digitale Sammlungen
    pages = {$config.kitodo.storagePid}
    // UID of dlfCore0
    solrcore = {$config.kitodo.solr.core}
    limit = {$config.kitodo.solr.searchLimit}
    // we activate fulltext here and search only in fulltext (see template)
    fulltext = 1
    // search only in current document
    searchIn = document
    // doesn't work due to javascript inclusion of autocomplete in header
    suggest = 0
    targetPid = {$config.kitodo.listView}
    // this feature doesn't work in our case. It always jumps to page 1
    showSingleResult = 0
    templateFile = {$config.kitodo.templates.searchFullText}
}

# --------------------------------------------------------------------------------------------------------------------
# ajax search in workview
# --------------------------------------------------------------------------------------------------------------------
plugin.tx_dlf_toolsSearchindocument {
    toolTemplateFile = {$config.kitodo.templates.searchInDocumentTool}
    pages = {$config.kitodo.storagePid}
    // UID of dlfCore0
    solrcore = {$config.kitodo.solr.core}
}

# --------------------------------------------------------------------------------------------------------------------
# collections
# --------------------------------------------------------------------------------------------------------------------
plugin.tx_dlf_collection {
    templateFile = {$config.kitodo.templates.collections}
}

# --------------------------------------------------------------------------------------------------------------------
# listview
# --------------------------------------------------------------------------------------------------------------------
plugin.tx_dlf_listview {
    templateFile = {$config.kitodo.templates.listView}
    # getTitle = 1
}

# --------------------------------------------------------------------------------------------------------------------
# metadata
# --------------------------------------------------------------------------------------------------------------------
plugin.tx_dlf_metadata {
    pages = {$config.kitodo.storagePid}
    excludeOther = 0
    linkTitle = 0
    getTitle = 0
    showFull = 1
    rootline = 1
    separator = #
    templateFile = {$config.kitodo.templates.metadata}
}

lib.kitodo.metadata.title = USER
lib.kitodo.metadata.title {
    includeLibs = typo3conf/ext/dlf/plugins/metadata/class.tx_dlf_metadata.php
    userFunc = tx_dlf_metadata->main
    pages = {$config.kitodo.storagePid}
    excludeOther = 1
    linkTitle = 0
    getTitle = 1
    showFull = 0
    rootline = 2
    separator = #
    templateFile = {$config.kitodo.templates.titledata}
}

lib.kitodo.metadata.full = USER
lib.kitodo.metadata.full {
    includeLibs = typo3conf/ext/dlf/plugins/metadata/class.tx_dlf_metadata.php
    userFunc = tx_dlf_metadata->main
    pages = {$config.kitodo.storagePid}
    excludeOther = 0
    linkTitle = 0
    getTitle = 0
    showFull = 1
    rootline = 1
    separator = #
    templateFile = {$config.kitodo.templates.metadata}
}

# --------------------------------------------------------------------------------------------------------------------
# pageview / workview
# --------------------------------------------------------------------------------------------------------------------
plugin.tx_dlf_pageview {
    pages = {$config.kitodo.storagePid}
    excludeOther = 0
    features =
    elementId = tx-dlf-map
    templateFile = {$config.kitodo.templates.pageView}
}

# --------------------------------------------------------------------------------------------------------------------
# thumbnail previews
# --------------------------------------------------------------------------------------------------------------------
plugin.tx_dlf_pagegrid {
    pages = {$config.kitodo.storagePid}
    limit = {$config.kitodo.pagegrid.limit}
    targetPid = #
    templateFile = {$config.kitodo.templates.gridView}
}
# --------------------------------------------------------------------------------------------------------------------
# table of contents
# --------------------------------------------------------------------------------------------------------------------
plugin.tx_dlf_toc {
    pages = {$config.kitodo.storagePid}
    excludeOther = 0
    targetPid.data = TSFE:page|uid
    templateFile = {$config.kitodo.templates.tableOfContents}
    menuConf {
        expAll = 0
        1 = TMENU
        1 {
            noBlur = 1
            wrap = <ul class="toc">|</ul>
            NO = 1
            NO {
                stdWrap {
                    htmlSpecialChars = 1
                    htmlSpecialChars.preserveEntities = 1
                    crop = 65 | &nbsp;... | 1
                    ifEmpty {
                        field = type
                        append.fieldRequired = volume
                        append = TEXT
                        append.field = volume
                        append.wrap = &nbsp;|
                    }
                    # show metadata type in front of menu item "Illustrierte Magazine"-Style
                    dataWrap = <span class="title"><span class="meta-type-icon meta-type-{field:type}">{field:type}</span> | </span> <span class="pagination">{field:pagination}</span>

                    # do not show metadata type
                    # dataWrap = <span class="tx-dlf-toc-title">|</span> <span class="tx-dlf-toc-pagination">{field:pagination}</span>
                }
                allWrap.cObject = TEXT
                allWrap.cObject {
                    insertData = 1
                    value = |
                    override.cObject = TEXT
                    override.cObject {
                        value = |<span class="basket-button">{field:basketButtonHref}</span>
                        if {
                            isTrue.field = basketButtonHref
                        }
                    }
                }
                doNotLinkIt.field = doNotLinkIt
                ATagTitle.field = title // type // orderlabel
                allWrap = <span class="a">|</span>
                allWrap.fieldRequired = doNotLinkIt
                wrapItemAndSub = <li>|</li>
            }
            IFSUB < .NO
            IFSUB.wrapItemAndSub = <li class="submenu">|</li>
            CUR < .NO
            CUR.wrapItemAndSub = <li class="current">|</li>
            CURIFSUB < .NO
            CURIFSUB.wrapItemAndSub = <li class="current-sub submenu">|</li>
            ACT < .NO
            ACT.wrapItemAndSub = <li class="active">|</li>
            ACTIFSUB < .NO
            ACTIFSUB.wrapItemAndSub = <li class="active-sub submenu">|</li>
        }
        2 < .1
        3 < .2
        4 < .3
        5 < .4
        6 < .5
        7 < .6
    }
}


# --------------------------------------------------------------------------------------------------------------------
# navigation
# --------------------------------------------------------------------------------------------------------------------

# --------------------------------------------------------------------------------------------------------------------
# foward and back buttons in page view
# --------------------------------------------------------------------------------------------------------------------
lib.kitodo.navigation.pagecontrol = USER
lib.kitodo.navigation.pagecontrol {
    includeLibs = typo3conf/ext/dlf/plugins/navigation/class.tx_dlf_navigation.php
    userFunc = tx_dlf_navigation->main
    pages = {$config.kitodo.storagePid}
    pageStep = 10
    templateFile = {$config.kitodo.templates.navigationPagecontrol}
}

# --------------------------------------------------------------------------------------------------------------------
# rotate and zoom buttons in page view
# --------------------------------------------------------------------------------------------------------------------
lib.kitodo.navigation.viewfunction = USER
lib.kitodo.navigation.viewfunction {
    includeLibs = typo3conf/ext/dlf/plugins/navigation/class.tx_dlf_navigation.php
    userFunc = tx_dlf_navigation->main
    pages = {$config.kitodo.storagePid}
    pageStep = 10
    templateFile = {$config.kitodo.templates.navigationViewfunction}
}

lib.kitodo.navigation.viewfunction_deactivated = USER
lib.kitodo.navigation.viewfunction_deactivated {
    includeLibs = typo3conf/ext/dlf/plugins/navigation/class.tx_dlf_navigation.php
    userFunc = tx_dlf_navigation->main
    pages = {$config.kitodo.storagePid}
    pageStep = 10
    templateFile = {$config.kitodo.templates.navigationViewfunction-deactivated}
}

# --------------------------------------------------------------------------------------------------------------------
# Tools like imagemanipulation, fulltext and downloads eg.
# --------------------------------------------------------------------------------------------------------------------
plugin.tx_dlf_toolbox {
    pages = {$config.kitodo.storagePid}
    fileGrpsImageDownload = MIN,DEFAULT,MAX

    # this overwrites the backend plugin settings --> avoid it here
    tools = tx_slubdlfhacks_pdfdownload,tx_dlf_toolsImagedownload,tx_dlf_toolsFulltext,tx_dlf_toolsImagemanipulation
    templateFile = {$config.kitodo.templates.toolbox}
}

plugin.tx_dlf_toolsPdf {
    pages = {$config.kitodo.storagePid}
    toolTemplateFile = {$config.kitodo.templates.toolsPdf}
}

plugin.tx_dlf_toolsFulltext {
    pages = {$config.kitodo.storagePid}
    toolTemplateFile = {$config.kitodo.templates.toolFullText}
}

plugin.tx_dlf_toolsImagemanipulation {
    pages = {$config.kitodo.storagePid}
    toolTemplateFile = {$config.kitodo.templates.toolsImageManipulation}
}

# --------------------------------------------------------------------------------------------------------------------
# newspaper navigation
# --------------------------------------------------------------------------------------------------------------------
lib.kitodo.newspaper.years = USER
lib.kitodo.newspaper.years {
    includeLibs = typo3conf/ext/dlf/plugins/newspaper/class.tx_dlf_newspaper.php
    userFunc = tx_dlf_newspaper->years
    pages = {$config.kitodo.storagePid}
    targetPid = {$config.kitodo.pageView}
    templateFile = {$config.kitodo.templates.newspaperYear}
}

lib.kitodo.newspaper.calendar = USER
lib.kitodo.newspaper.calendar {
    includeLibs = typo3conf/ext/dlf/plugins/newspaper/class.tx_dlf_newspaper.php
    userFunc = tx_dlf_newspaper->calendar
    pages = {$config.kitodo.storagePid}
    targetPid = {$config.kitodo.pageView}
    templateFile = {$config.kitodo.templates.newspaperCalendar}
}

[userFunc = user_dlf_docTypeCheck(newspaper)]
page.10.variables {
  isNewspaper = TEXT
  isNewspaper.value = newspaper_anchor
}
[END]

[userFunc = user_dlf_docTypeCheck(year)]
page.10.variables {
  isNewspaper = TEXT
  isNewspaper.value = newspaper_year
}
[END]

[userFunc = user_dlf_docTypeCheck(issue)]
page.10.variables {
  isNewspaper = TEXT
  isNewspaper.value = newspaper_issue
}
[END]
