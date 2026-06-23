# Grouped Search Results - TYPO3 Find Integration

## Overview

Grouped search results allow you to organize Solr search results by a specific field or query, displaying the first result from each group prominently with a "more" button to expand and show additional results within that group.

## What is Grouping?

Instead of displaying search results as a flat list, grouping organizes results into collapsible groups. Each group represents documents that share a common attribute (e.g., same collection, author, or year range).

**Example:**
```
Collection A
├─ Document 1 (shown by default)
├─ Document 2 (hidden, click "more" to expand)
├─ Document 3
└─ Document 4

Collection B  
├─ Document 5 (shown by default)
├─ Document 6 (hidden)
└─ Document 7
```

## TypoScript Configuration

### Basic Setup

Enable grouping in your TypoScript configuration:

```typoscript
plugin.tx_find.settings {
    grouping {
        # Enable grouping
        enabled = 1
        
        # Field to group by
        field = collection_id
        
        # Number of results per group to display
        limit = 5
    }
}
```

### Complete Configuration Example

```typoscript
plugin.tx_find.settings {
    # Standard search settings
    standardFields {
        title = title
        snippet = text
    }
    
    # Grouping configuration
    grouping {
        # Activate/deactivate grouping
        enabled = 1
        
        # Single field grouping
        field = collection_id
        
        # OR use multiple fields (commented out if using single field)
        # fields {
        #     0 = collection_id
        #     1 = document_type
        # }
        
        # Maximum documents per group to retrieve
        limit = 10
        
        # Start offset within groups
        offset = 0
        
        # Sort documents within each group
        sort = title asc
        
        # Calculate total number of groups
        numberOfGroups = 1
        
        # Truncate group count (optimization)
        truncate = 0
        
        # Cache settings (0-100)
        cachePercentage = 0
    }
}
```

### Field-Based Grouping

Group results by a single Solr field:

```typoscript
grouping {
    enabled = 1
    field = collection_id
    limit = 20
    sort = date_indexed desc
}
```

**Use Cases:**
- Group by `collection_id` - Results organized by collection
- Group by `author` - Results organized by author
- Group by `document_type` - Group by document type (manuscript, book, etc.)
- Group by `year` - Group by publication year

### Multiple Field Grouping

Group by multiple fields hierarchically:

```typoscript
grouping {
    enabled = 1
    fields {
        0 = collection_id
        1 = author
        2 = year
    }
    limit = 15
}
```

This creates nested groups: Collection → Author → Year

### Query-Based Grouping

Group results by Solr queries (e.g., date ranges):

```typoscript
grouping {
    enabled = 1
    queries {
        0 {
            label = Before 1900
            query = year:[* TO 1899]
        }
        1 {
            label = 1900-1950
            query = year:[1900 TO 1950]
        }
        2 {
            label = 1950-2000
            query = year:[1951 TO 2000]
        }
        3 {
            label = After 2000
            query = year:[2001 TO *]
        }
    }
    limit = 10
}
```

## Configuration Parameters Reference

### Core Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `enabled` | boolean | 1 | Enable/disable grouping |
| `field` | string | - | Single grouping field |
| `fields` | array | - | Multiple grouping fields |
| `queries` | array | - | Query-based grouping definition |

### Display Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `limit` | integer | 10 | Max results per group to retrieve |
| `offset` | integer | 0 | Start position within group results |
| `sort` | string | - | Sort documents in groups (e.g., `title asc`) |

### Performance Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `numberOfGroups` | boolean | 1 | Calculate total group count |
| `truncate` | boolean | 0 | Limit group count calculation |
| `cachePercentage` | integer | 0 | Query result caching (0-100) |

## Template Implementation

The template automatically handles grouped results when `groupingActive = 1`:

```html
<f:if condition="{groupingActive}">
    <f:for each="{groupedResults.valueGroups}" as="group" iteration="groupIterator">
        <li>
            <!-- First document in group (always shown) -->
            <f:render partial="Display/Result" arguments="{document: group.primaryDocument, config: config}"/>
            
            <!-- Expand/collapse button -->
            <button
                type="button"
                class="tx-dlf-morevolumes"
                title="{f:translate(key: 'find.list.more', extensionName: 'slub_digitalcollections')}"
                aria-expanded="false"
                aria-controls="volume-list-{config.uid}-{groupIterator.cycle}">
                {f:translate(key: 'find.list.more', extensionName: 'slub_digitalcollections')}
            </button>
            
            <!-- Additional group documents (hidden by default) -->
            <ol class="tx-dlf-volume" id="volume-list-{config.uid}-{groupIterator.cycle}" aria-hidden="true">
                <f:for each="{group.documents}" as="document">
                    <li>
                        <f:render partial="Display/Result" arguments="{document: document, config: config}"/>
                    </li>
                </f:for>
            </ol>
        </li>
    </f:for>
</f:if>
```

### Template Variables

| Variable | Type | Description |
|----------|------|-------------|
| `{groupingActive}` | boolean | True if grouping is enabled |
| `{groupedResults.valueGroups}` | array | Array of grouped results |
| `{group.value}` | string | Group identifier/name |
| `{group.numFound}` | integer | Total results in this group |
| `{group.documents}` | array | Documents in current group |
| `{groupIterator.cycle}` | string | Unique group identifier |

## URL Parameters

Override TypoScript settings via URL parameters:

```
# Single field grouping
?tx_find_find[groupField]=author

# Multiple fields
?tx_find_find[groupFields][0]=collection
&tx_find_find[groupFields][1]=author

# Query grouping
?tx_find_find[groupQuery][0]=year:[1900 TO 1950]

# Parameters
?tx_find_find[groupLimit]=20
?tx_find_find[groupOffset]=0
?tx_find_find[groupSort]=date desc
```

## Practical Examples

### Example 1: Group by Collection

```typoscript
plugin.tx_find.settings.grouping {
    enabled = 1
    field = collection_id
    limit = 10
    sort = title asc
}
```

**Result:** Search results are grouped by collection. Each group shows the first 10 documents sorted by title, with remaining documents collapsible under "more".

### Example 2: Group by Author with Limit

```typoscript
plugin.tx_find.settings.grouping {
    enabled = 1
    field = author
    limit = 5
    sort = date_indexed desc
    numberOfGroups = 1
}
```

**Result:** Results grouped by author, showing newest 5 documents per author first.

### Example 3: Group by Time Periods

```typoscript
plugin.tx_find.settings.grouping {
    enabled = 1
    queries {
        0.query = year:[1800 TO 1850]
        1.query = year:[1851 TO 1900]
        2.query = year:[1901 TO 1950]
        3.query = year:[1951 TO *]
    }
    limit = 15
}
```

**Result:** Results organized into historical periods, showing up to 15 documents per period.

### Example 4: Hierarchical Grouping

```typoscript
plugin.tx_find.settings.grouping {
    enabled = 1
    fields {
        0 = institution
        1 = collection_type
    }
    limit = 20
    numberOfGroups = 1
    truncate = 0
}
```

**Result:** Results grouped first by institution, then by collection type within each institution.

## Disabling Grouping

To disable grouping in specific contexts:

```typoscript
plugin.tx_find.settings.grouping.enabled = 0
```

Or disable via URL:
```
?tx_find_find[grouping][enabled]=0
```

## Performance Considerations

1. **Limit per Group** - Set appropriate `limit` to balance completeness and performance
2. **numberOfGroups** - Set to `0` if total group count not needed
3. **truncate** - Enable for large datasets to avoid expensive calculations
4. **Indexed Fields** - Ensure grouping fields are indexed in Solr
5. **Field Type** - Use string fields (not NUMERIC docvalues) for best performance

## Troubleshooting

### Results Not Grouped

- Verify `grouping.enabled = 1` in TypoScript
- Check that field exists in Solr index: `?debug=true` in Solr query
- Clear TYPO3 cache
- Verify field name matches exactly

### Incomplete Results

- Increase `limit` parameter
- Reduce number of grouping fields
- Check Solr query limitations

### Performance Issues

- Lower `limit` value
- Set `numberOfGroups = 0` if not needed
- Enable `truncate = 1`
- Use single field grouping instead of multiple fields

## References

- [TYPO3 Find Documentation](https://github.com/subugoe/find)
- [Apache Solr Grouping](https://solr.apache.org/guide/result-grouping.html)
- [Solarium PHP Client](https://solarium.readthedocs.io/)

## Support

- SLUB Dresden: typo3@slub-dresden.de
- GitHub Issues: https://github.com/slub/slub_digitalcollections/issues
