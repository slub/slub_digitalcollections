###################################################
# Page TS settings
###################################################

mod.web_layout.BackendLayouts {
	kitodo {
		title = Kitodo: Digital Collections - Workview
		config {
			backend_layout {
                    colCount = 1
                    rowCount = 1
                    rows {
                        1 {
                            columns {
                                1 {
                                    name = Main
                                    colPos = 0
                                }
                            }
                        }
                    }
                }
		}
		icon = EXT:slub_digitalcollections/Resources/Public/Images/BackendLayouts/workview.png
	}
	emptyworkview {
		title = Kitodo: Digital Collections - Empty Workview
		config {
			backend_layout {
                    colCount = 1
                    rowCount = 1
                    rows {
                        1 {
                            columns {
                                1 {
                                    name = Main
                                    colPos = 0
                                }
                            }
                        }
                    }
                }
		}
		icon = EXT:slub_digitalcollections/Resources/Public/Images/BackendLayouts/workview.png
	}
}