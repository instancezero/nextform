NextForm
===
NextForm is a PHP based form generator for application developers. The primary
genesis of the package was a need to programmatically transform and generate
forms. Although developed as a stand-alone package, NextForm was designed to
integrate with Laravel. This integration is available through the
NextForm-Laravel package.

In addition to application-generated forms, NextForm is
designed to eliminate other irritants with online forms:

- Most forms are a mix of PHP, HTML, and the templating language of the
enclosing application. Since forms frequently transition between HTML,
template markup, Javascript and PHP several times per form the results can be
difficult to read, format, and understand.
- Forms are tightly coupled to a specific HTML/CSS framework. Adapting to
framework changes is more difficult than it should be, and maintaining
multiple frameworks can be a major headache.
- Applications frequently need to present several highly similar forms, each with
variations depending on the environment or the user viewing the form. If a
single form accounts for multiple user contexts, it can be riddled with
conditionals that make the code difficult to understand and maintain.
- Since forms are often exclusively defined in the view part of an application,
they are traditionally inflexible and difficult to transform. NextForm provides
for the programmatic transformation of forms before they are generated. This
gives MVC applications capabilities that are usually only implemented via
client-side frameworks like Vue.
- Frameworks can do a good job of presentation and validation, but tend to not
offer support for client-side interactions between form elements.

Too many forms are brittle, full of duplication, difficult to maintain, and a
barrier to rapid development. NextForm offers good reusability, concise form
specifications, fine grained access control, and portability across client-side
frameworks.

Development Status
---
As of this date, NextForm is new and under active development. Tests provide code
coverage over 85%, but the code is just now being deployed into the application
that requires it. The API should be stable or at least any changes should be
backwards compatible, but this isn't guaranteed, and some minor bugs are
expected.

The next render target for NextForm will be Vue. The current architecture was
developed with this target in mind, but it is highly likely that actual
implementation will force changes, some of which might impact existing
deployments. There is no timeline for this. If you want to help, get in touch
via Gitlab.

Installation
---

For the stand-alone package require `abivia\nextform`. The tests in this
package contain several examples of usage.

For Laravel integration require `abivia\nextform-laravel`. A simple but evolving
(and thus potentially unstable) example of NextForm in use in Laravel can be
found on our Gitlab account (link coming soon).

NextForm Architecture
---

NextForm separates form generation into a data schema, form definitions, and
rendering engines. NextForm also supports field-level access control and
translations. NextForm's data and schema definitions are JSON or YAML based,
loaded into structures that can be manipulated in PHP. Forms can also be
defined entirely in PHP, allowing for application-driven form generation.

A Schema defines presentation and validation information for data that can be
displayed on a form. A schema can contain multiple segments. Each segment
typically represents a data model or a database table. Separating data elements
allows for uniform presentation and validation of that data across multiple
forms. Change the definition in one place and NextForm will change it
everywhere.

Forms are composed of elements. These elements can be connected to data in
schemas. In the simplest case, a form can be created from a list of data
elements and a button. Forms can also define events that define interactions
between elements. Using this mechanism form elements can be hidden or made
visible based on user actions.

Many applications also need to maintain several variations of similar forms.
Information contained on a form seen by a user with guest access might contain
a subset of information on a manager level form, and an administrator might
be able to change information that is only read-only for a manager. In
most cases, this requires the implementation of multiple forms with
significant duplication. NextForm's access control system allows a developer
to specify access and visibility levels for each user or user role in the
schema, allowing for a single form definition that meets all requirements.


Form Layout Overview
---

A form is a collection of form elements.

Form elements can be simple or compound.

The simple elements are static text, buttons (submit, reset, etc.), HTML, and
input elements connected to data.

There are two types of compound elements: cells and groups. A cell contains
a set of elements that are presented as a single unit. A section is a collection
of related fields. Sections can contain cells, cells cannot contain sections.

All elements can be members of zero or more groups. Groups are used to
enable/disable and hide/show form elements depending on user activity.

Data Schema Overview
---
A schema defines the data objects that can be assembled into forms.

The schema has provisions for data visibility based on a simple access
control system. Data objects can be read/write, read only, hidden, or not present
depending on the user's permissions. Schemas can be loaded from JSON or YAML
files, or be generated by the application.

Each schema can have multiple segments. The intent is that segments map to
models or other data structures used to persist data, but this is not a requirement.
Each segment can contain one or more scalar objects.

Objects must have a unique name within the segment. Each object defines
characteristics including the storage format and size, how the data should be
displayed, text labels that describe it, etc.

Translation Overview
---
NextForm translation uses Laravel's translator interface. Specifically the
`get()` method. Only simple translations with no parameters
are supported. A translation instance is not required. If none is provided,
no translations occur.

This should make adapting to other translation systems straightforward.

Access Control Overview
---
NextForm integrates a very simple role-based permissions system. Access
rights can be set by role for an entire segment or for individual objects
within a segment. If no access control mechanism is set, all data elements
in the form are writeable.

The `AccessInterface` contract defines the requirements for a custom
implementation of the access control function.

The access permissions are:

- None. The object is not rendered on the form.
- Hide. The object is embedded in the form as a hidden element.
- View. The object is displayable but not editable.
- Write. The object is visible and modifiable by the user.

Rendering Overview
---

NextForm's schema and form definitions have been designed with the intent of
being as independent of the final output as possible. By separating this
information from the form implementation, the hope is that NextForm can support
not only common HTML/CSS frameworks such as Bootstrap, but also client-side
Javascript systems such as Vue. The `RenderInterface` specifies a simple
requirement for the implementation of additional environments.

NextForm currently provides two render engines:

- The Bootstrap4 engine generates event-capable, accessible forms.
Render options support horizontal and vertical layouts and customization features.
- The SimpleHtml engine generates basic HTML forms with no native event
processing.


The Schema Data Structure
===

At the top level a schema has two properties, default and segments.

default
---

The default property specifies common values for all other elements. At this
point that includes only labels. Thus the default can be used to set a
common error message for all objects. This message can be overridden in
an object definition.

segments
---

A segment contains a name, a list of objects, and an optional list of the
object names that constitute a primary key for the data in the segment.

segments.primary
---

This is either a string consisting of one object name or an array of object
names.

segments.objects
---
Each object has these properties:

- `name` The name of the object. Names must be unique within a segment.
- `description` An optional string that describes the object.
- `labels` Defines labels associated with the object.
- `population` If the object can take values from a finite set of possible
values, for example in the case of radio buttons, this defines the
allowable values.
- `presentation` The presentation describes the normal representation of the
object on the form.
- `store` The store describes the format and size of the data when it is stored.
- `validation` Validation specifies the rules for an acceptable value.

segments.objects.labels
---

Labels can be a simple string or an object. If labels is a string, it
is used as the `heading` property. The object can have these
properties, all of which are optional:

- `accept` Text to be displayed when a field passes validation.
- `after` Text that immediately follows the input element. For example, if
the object is intended to be an amount in whole dollars, this might be '.00'
to aide the user in entering a whole number.
- `before` Text that immediately precedes the input element. For example,
this might be 'https:' to indicate a link, or '@' for a social media handle.
- `confirm` If this is a string, it is the heading to be displayed when this
is a confirmation field. For example 'Confirm password'. If it is an object
then it can have any label property except `confirm`. These strings will
override the parent strings on a confirmation field.
- `error` The text to be displayed when a user input fails validation.
- `heading` A title for the input object.
- `help` Text to be displayed to assist the user in entering a value.
- `inner` Text displayed inside the input element, for example as a placeholder.
- `translate` A flag that indicates if the labels are to be translated.
The default value is true.

segments.objects.population
---

A population has the following properties:

- `list` For fixed lists, this is a list of options, the possible values that
the object can adopt (see segments.objects.population.option).
- `sidecar` The sidecar is an arbitrary JSON encoded string that will be
attached to the object in the generated code.
- `source` How the population is created. Currently the only valid source
is 'fixed'. The possible values are contained in the list property.
- `translate` A flag that indicates if the values are to be translated.
The default value is true.

segments.objects.population.option
---

The possible values in a population are stored in an Option. Options have
these properties:

- `enabled` A flag that indicates if this option is currently selectable.
Default is true.
- `label` The text displayed to the user for this option.
- `memberOf` A list of groups that this option belongs to.
- `name` An optional name for the option that can be used to refer to it on the
rendered form.
- `sidecar` The sidecar is an arbitrary JSON encoded string that will be
attached to the object in the generated code.
- `value` The value returned when this option has been selected at the time
of form submission.

Options also support a shorthand notation, a string of the form "label:value".
Thus `list: ["One:1", "Two:2", "More:X"]` is a valid list definition. Strings
and object forms can be mixed in the same list.

segments.objects.presentation
---

The presentation specifies how an object should be displayed on the form. Not
all properties make sense for all input types. Anything that doesn't make sense
is ignored. A presentation has these properties:

- `cols` The number of columns, using a 12 column grid system, to use to
display the object.
- `confirm` A flag indicating if NextForm should generate a second input to
confirm that the user entered a correct value.
- `rows` The number of rows to use when displaying a combo, text area, etc.
- `type` The input type. Possible values include: 'button', 'checkbox', 'color',
'date', 'datetime-local', 'email', 'file', 'hidden', 'image', 'month',
'number', 'password', 'radio', 'range', 'reset', 'search', 'select',
'submit', 'tel', 'text', 'textarea', 'time', 'url', and 'week'.

segments.objects.store
---

The store defines how the object will be persisted in storage. A store has
these properties:

- `size` A string indicating the maximum size of the object. This is often
but not always an integer.
- `type' One of 'blob', 'date', 'decimal', 'float', 'int', 'string', or 'text'.

segments.objects.validation
---

The validation defines acceptable values for the object. The validation
properties are:

- `accept` For file type presentations, this is an array of acceptable
file patterns
- `capture` For file types that are image or video, sets the data source.
- `maxLength` The maximum length of text based inputs.
- `maxValue` The maximum value of numeric based inputs.
- `minLength` The minimum length of text based inputs.
- `minValue` The minimum value of numeric based inputs.
- `multiple` A flag that is true if an object can accept multiple values.
Default false.
- `pattern` A Javascript regular expression that defines acceptable text values.
- `required` A flag indicating that a value is required for this object.
Default false.
- `step` The size of increments/decrements for numeric and range inputs.
- `translatePattern` A flag indicating that the `pattern` should be translated.
Default false.

The Form Data Structure
===

[coming in the next round of docs]
Until then, see tests/integrated for an extensive example.
