# GroupedSolrServiceProvider - Dokumentation

## Übersicht

Die `GroupedSolrServiceProvider` Klasse erweitert die Standard-Solr-Integration von TYPO3 Find um vollständige Solr Grouping-Funktionalität.

## Features

✅ **Field-basiertes Grouping** - Gruppierung nach einem oder mehreren Feldern  
✅ **Query-basiertes Grouping** - Gruppierung nach Solr-Queries  
✅ **Erweiterte Parameter** - Vollständige Kontrolle über Solr Grouping-Parameter  
✅ **URL-Parameter** - Überschreibung der TypoScript-Konfiguration via URL  
✅ **Flexible Konfiguration** - Einfache TypoScript-Konfiguration  
✅ **Logging & Debugging** - Ausführliche Fehlerprotokollierung

## Installation & Aktivierung

### 1. Provider aktivieren

In Ihrer TypoScript-Konfiguration (z.B. `setup.typoscript`):

```typoscript
plugin.tx_find.settings {
    connections {
        default {
            # GroupedSolrServiceProvider verwenden
            provider = Slub\SlubDigitalcollections\Service\GroupedSolrServiceProvider
            
            options {
                scheme = http
                host = localhost
                port = 8983
                path = /solr/
                core = your_core
            }
        }
    }
}
```

### 2. Grouping konfigurieren

Siehe `grouping-example.typoscript` für vollständige Konfigurationsoptionen.

**Minimale Konfiguration:**

```typoscript
plugin.tx_find.settings {
    grouping {
        enabled = 1
        field = uid
        limit = 100
    }
}
```

## Verwendungsszenarien

### Szenario 1: Gruppierung nach Sammlungen

```typoscript
grouping {
    enabled = 1
    field = collection_id
    limit = 10
    sort = title asc
}
```

**Ergebnis:** Dokumente werden nach `collection_id` gruppiert, max. 10 Dokumente pro Sammlung, sortiert nach Titel.

### Szenario 2: Mehrere Grouping-Felder

```typoscript
grouping {
    enabled = 1
    fields {
        0 = collection_id
        1 = document_type
    }
    limit = 20
}
```

**Ergebnis:** Dokumente werden nach Sammlung UND Dokumenttyp gruppiert.

### Szenario 3: Gruppierung nach Jahrzehnten

```typoscript
grouping {
    enabled = 1
    queries {
        0 {
            query = year:[1900 TO 1919]
        }
        1 {
            query = year:[1920 TO 1939]
        }
        2 {
            query = year:[1940 TO 1959]
        }
        3 {
            query = year:[1960 TO 1979]
        }
        4 {
            query = year:[1980 TO *]
        }
    }
    limit = 50
}
```

**Ergebnis:** Dokumente werden in Jahrzehnt-Gruppen organisiert.

### Szenario 4: Dynamische Gruppierung via URL

Benutzer können die Gruppierung per URL-Parameter ändern:

```
# Nach Autor gruppieren
?tx_find_find[groupField]=author

# Nach mehreren Feldern
?tx_find_find[groupFields][0]=author&tx_find_find[groupFields][1]=year

# Limit ändern
?tx_find_find[groupLimit]=5
```

## Template-Integration

### Zugriff auf gruppierte Ergebnisse

In Ihren Fluid-Templates stehen folgende Variablen zur Verfügung:

- `{groupedResults}` - Array mit gruppierten Ergebnissen
- `{groupCount}` - Anzahl der Gruppen
- `{matches}` - Gesamtanzahl der Treffer
- `{groupingActive}` - Boolean (true wenn Grouping aktiv)

### Beispiel: Einfache Ausgabe

```html
<f:if condition="{results.groupingActive}">
    <h2>Gruppierte Ergebnisse</h2>
    <p>Gefunden: {results.matches} Dokumente in {results.groupCount} Gruppen</p>
    
    <f:for each="{results.groupedResults}" as="fieldGroups" key="fieldName">
        <h3>Gruppiert nach: {fieldName}</h3>
        
        <f:for each="{fieldGroups.groups}" as="group">
            <div class="group">
                <h4>{group.value} ({group.numFound})</h4>
                
                <ul>
                    <f:for each="{group.documents}" as="document">
                        <li>
                            <strong>{document.title}</strong>
                            <span class="meta">{document.author}, {document.year}</span>
                        </li>
                    </f:for>
                </ul>
            </div>
        </f:for>
    </f:for>
</f:if>
```

### Beispiel: Erweiterte Ausgabe mit Pagination

```html
<f:if condition="{results.groupingActive}">
    <f:for each="{results.groupedResults}" as="fieldGroups" key="fieldName">
        <section class="grouped-results">
            <header>
                <h2>Gruppiert nach: {fieldName}</h2>
                <p class="statistics">
                    {fieldGroups.numberOfGroups} Gruppen, 
                    {fieldGroups.matches} Treffer gesamt
                </p>
            </header>
            
            <f:for each="{fieldGroups.groups}" as="group" iteration="groupIterator">
                <article class="group-container">
                    <h3 class="group-title">
                        {group.value}
                        <span class="badge">{group.numFound}</span>
                    </h3>
                    
                    <div class="documents">
                        <f:for each="{group.documents}" as="document" iteration="iterator">
                            <f:render 
                                partial="Result/Document" 
                                arguments="{document: document, position: iterator.index}" 
                            />
                        </f:for>
                    </div>
                    
                    <f:if condition="{group.numFound} > {settings.grouping.limit}">
                        <a href="#" class="show-more" 
                           data-group="{group.value}"
                           data-field="{fieldName}">
                            Mehr anzeigen ({group.numFound - settings.grouping.limit} weitere)
                        </a>
                    </f:if>
                </article>
            </f:for>
        </section>
    </f:for>
</f:if>
```

## Konfigurationsparameter

### Basis-Parameter

| Parameter | Typ | Default | Beschreibung |
|-----------|-----|---------|--------------|
| `enabled` | bool | true | Grouping aktivieren/deaktivieren |
| `field` | string | - | Einzelnes Grouping-Feld |
| `fields` | array | - | Mehrere Grouping-Felder |
| `queries` | array | - | Query-basiertes Grouping |

### Performance-Parameter

| Parameter | Typ | Default | Beschreibung |
|-----------|-----|---------|--------------|
| `limit` | int | -1 | Dokumente pro Gruppe (-1 = alle) |
| `offset` | int | 0 | Start-Position in Gruppen |
| `cachePercentage` | int | 0 | Query-Cache (0-100) |
| `numberOfGroups` | bool | true | Gruppenzahl berechnen |
| `truncate` | bool | false | Gruppenzahl auf Limit begrenzen |

### Darstellungs-Parameter

| Parameter | Typ | Default | Beschreibung |
|-----------|-----|---------|--------------|
| `sort` | string | - | Sortierung innerhalb Gruppen |
| `format` | string | grouped | Format: `grouped` oder `simple` |
| `facets` | bool | false | Facetten für gruppierte Ergebnisse* |

**Hinweis zu Facets & Grouping:** Das Kombinieren von Facets mit Grouping erfordert spezielle Solr-Schema-Konfiguration. Grouping-Felder müssen den docvalues-Typ `SORTED` haben (nicht `NUMERIC`). Der Default ist `false`, um Fehler zu vermeiden.

## Performance-Tipps

1. **Limit setzen**: Verwenden Sie `limit`, um die Ergebnismenge zu begrenzen
2. **numberOfGroups optional**: Setzen Sie auf `false`, wenn die Gesamtzahl nicht benötigt wird
3. **Truncate bei großen Sets**: Aktivieren Sie `truncate = true` für bessere Performance
4. **Field-Grouping bevorzugen**: Schneller als Query-basiertes Grouping
5. **Solr-Index optimieren**: Stellen Sie sicher, dass Grouping-Felder indexiert sind
6. **Cache nutzen**: `cachePercentage` mit Vorsicht einsetzen (erhöht RAM-Nutzung)

## Debugging

### Log-Ausgaben prüfen

Die Klasse schreibt Debug-Informationen in das TYPO3-Log:

```php
// In LocalConfiguration.php oder AdditionalConfiguration.php
$GLOBALS['TYPO3_CONF_VARS']['LOG']['Slub']['SlubDigitalcollections']['writerConfiguration'] = [
    \Psr\Log\LogLevel::DEBUG => [
        \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
            'logFile' => 'typo3temp/var/log/grouping.log'
        ],
    ],
];
```

### Häufige Probleme

**Problem:** Keine gruppierten Ergebnisse

- ✓ Prüfen Sie `enabled = 1` in der Konfiguration
- ✓ Prüfen Sie, ob das Grouping-Feld im Solr-Index existiert
- ✓ Prüfen Sie die Log-Ausgaben

**Problem:** Fehler "unexpected docvalues type NUMERIC" beim Faceting

Dieser Fehler tritt auf, wenn `facets = true` gesetzt ist und das Grouping-Feld den falschen docvalues-Typ hat.

**Lösung 1 (empfohlen):** Facets deaktivieren
```typoscript
grouping {
    facets = false  # Standard-Faceting verwenden
}
```

**Lösung 2:** Solr-Schema anpassen (erfordert Re-Indexierung)
```xml
<!-- In schema.xml: Grouping-Feld als String mit SORTED docvalues -->
<field name="uid" type="string" indexed="true" stored="true" docValues="true"/>
```

**Lösung 3:** Anderes Grouping-Feld verwenden
```typoscript
grouping {
    field = collection_id  # Verwenden Sie ein String-Feld
    facets = true
}
```

**Problem:** Zu wenige Ergebnisse pro Gruppe

- ✓ Erhöhen Sie den `limit` Parameter
- ✓ Prüfen Sie `offset` Einstellungen

**Problem:** Performance-Probleme

- ✓ Setzen Sie niedrigeren `limit`
- ✓ Deaktivieren Sie `numberOfGroups`
- ✓ Aktivieren Sie `truncate`
- ✓ Reduzieren Sie die Anzahl der Grouping-Felder

## URL-Parameter Referenz

Alle TypoScript-Parameter können via URL überschrieben werden:

```
# Einzelnes Feld
?tx_find_find[groupField]=author

# Mehrere Felder
?tx_find_find[groupFields][0]=author
&tx_find_find[groupFields][1]=year

# Queries
?tx_find_find[groupQuery][0]=year:[1900 TO 1950]

# Parameter
?tx_find_find[groupLimit]=50
?tx_find_find[groupOffset]=0
?tx_find_find[groupSort]=title asc
?tx_find_find[groupFormat]=simple
```

## Weitere Informationen

- **Solr Dokumentation**: https://solr.apache.org/guide/result-grouping.html
- **Solarium Dokumentation**: https://solarium.readthedocs.io/en/stable/queries/select-query/building-a-select-query/components/grouping/
- **TYPO3 Find**: https://github.com/subugoe/find

## Support

Bei Fragen oder Problemen:
- SLUB Dresden: typo3@slub-dresden.de
- GitHub Issues: https://github.com/slub/slub_digitalcollections/issues
