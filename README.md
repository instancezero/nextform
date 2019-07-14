pre-notes: just added the basic access class, with simple roles. Need unit tests for these.

Abivia Forms
===
Form processing has two major components, data specification and form layouts.

Data Specifications
===

A data specification defines all the variable elements that can be assembled into forms.

Data elements are organized into segments. The intent is that segments map to objects used
to load or store data, but this is not a requirement. A segment contains a collection of
scalar properties.

Each property has a name that is unique within its segment. Required fields specify how the
property is defined in terms of a data store (type, size, etc.), and how the property should
be displayed. Optional fields define the text labels associated with the property and
access control rules.

```json
{
    "name": "duration",
    "store": {
        "type": "int",
        "size": "10"
    },
    "labels": {
        "after": "Seconds",
        "before": "Delay time:",
        "placeholder": "0"
    },
    "presentation": {
        "cols": "1",
        "type": "text"
    }
}
```

Form layouts
===

A form is a collection of form elements.

Form elements can be simple or compound.

There are two types of compound elements: cells and groups. A cell encapsulates a set of
elements that are presented as a single unit. A group is a collection of related fields.
Groups can contain cells, cells cannot contain groups.
