# --------------------------------------------------------------------------------------------------------------------
# Typoscript setup for SLUB Digital Collections
# --------------------------------------------------------------------------------------------------------------------

# --------------------------------------------------------------------------------------------------------------------
# pageview navigation
# --------------------------------------------------------------------------------------------------------------------

lib.navigation.kitodo = HMENU
lib.navigation.kitodo {
    special = list
    special.value = {$config.kitodo.viewerNavigationPids}
    includeNotInMenu = 1
    1 = TMENU
    1 {
        expAll = 1
        insertData = 1
        noBlur = 1
        NO = 1
        NO {
            wrapItemAndSub = <li>|</li>
            ATagTitle.field = description // title
        }
    }
}

# --------------------------------------------------------------------------------------------------------------------
# render new page in pageview / workview
# --------------------------------------------------------------------------------------------------------------------

page {
    # assume "10"
    10 {
        # Template names will be generated automatically by converting the applied
        # backend_layout, there is no explicit mapping necessary anymore.
        #
        # BackendLayout Key
        # subnavigation_right_2_columns -> SubnavigationRight2Columns.html
        #
        # Backend Record
        # uid: 1 -> 1.html
        #
        # Database Entry
        # value: -1 -> None.html
        # value: pagets__subnavigation_right_2_columns -> SubnavigationRight2Columns.html
        templateName = TEXT
        templateName {
            cObject = TEXT
            cObject {
                data = pagelayout
                required = 1
                case = uppercamelcase
                split {
                    token = pagets__
                    cObjNum = 1
                    1.current = 1
                }
            }
            ifEmpty = Default
        }

        layoutRootPaths.10 = {$config.layoutRootPath}
        partialRootPaths.10 = {$config.partialRootPath}
        templateRootPaths.10 = {$config.templateRootPath}
        settings {
            rootPage {
                pid = {$config.kitodo.rootPage.pid}
                title = {$config.kitodo.rootPage.title}
                cssClass = {$config.kitodo.rootPage.cssClass}
            }

            termsOfUsePid = {$config.kitodo.termsOfUsePid}

            matomo {
                hostname = {$config.piwik_hostname}
                siteId = {$config.piwik_idsite}
                setdomains = {$config.piwik_domains}
            }

            collections {
                solrHost = {$config.kitodo.solr.host}/{$config.kitodo.solr.coreName}
                solrTimeout = {$config.kitodo.solr.timeout}
            }

            showProviderLogo = 0
        }
        variables {
                content < styles.content.get
                sidebar < styles.content.get
                sidebar.select.where = colPos=1

                isKitodoPageView = TEXT
                isKitodoPageView.value = 1

                docTitle = TEXT
                docTitle {
                    value = {register:postTitle}
                    insertData = 1
                }

                # kitodo vars
                gp-id = TEXT
                gp-id {
                    data = GP:tx_dlf|id
                    ifEmpty = 1
                    stdWrap.intval = 1
                }

                gp-page = TEXT
                gp-page {
                    data = GP:tx_dlf|page
                    ifEmpty = 1
                    stdWrap.intval = 1
                }

                gp-page2 = TEXT
                gp-page2 {
                    cObject = TEXT
                    cObject.data = GP:tx_dlf|page
                    cObject.wrap = | +1
                    prioriCalc = 1
                }

                gp-double = TEXT
                gp-double.data = GP:tx_dlf|double

                gp-pagegrid = TEXT
                gp-pagegrid.data = GP:tx_dlf|pagegrid

        }
    }
}

# --------------------------------------------------------------------------------------------------------------------
# load kitodo configs depending on TYPO3 version
# --------------------------------------------------------------------------------------------------------------------
# The condition is necessary because you cannot nest TypoScript conditions. But inside the included TypoScript files,
# more conditions appear.

# for TYPO3 8.7 and 9.5 with Kitodo.Presentation 3.x
<INCLUDE_TYPOSCRIPT: source="FILE:EXT:slub_digitalcollections/Configuration/TypoScript/Plugin/Kitodo/setup9.typoscript" condition="Slub\\SlubDigitalcollections\\Helpers\\CoreVersionCondition >= 8.7">
<INCLUDE_TYPOSCRIPT: source="FILE:EXT:slub_digitalcollections/Configuration/TypoScript/Plugin/singleCollection.typoscript" condition="Slub\\SlubDigitalcollections\\Helpers\\CoreVersionCondition >= 9.5">
# for TYPO3 7.6 with Kitodo.Presentation 2.x
<INCLUDE_TYPOSCRIPT: source="FILE:EXT:slub_digitalcollections/Configuration/TypoScript/Plugin/Kitodo/setup7.ts" condition="Slub\\SlubDigitalcollections\\Helpers\\CoreVersionCondition < 8.0">

# --------------------------------------------------------------------------------------------------------------------
# body class overrides
# --------------------------------------------------------------------------------------------------------------------
[globalVar = TSFE:id={$config.kitodo.collectionView}]
page.bodyTag = <body class="collections">
# render content parts only
[END]

[globalVar = TSFE:id={$config.kitodo.listView}]
page.bodyTag = <body class="listview">
[END]
