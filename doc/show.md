General syntax (resembles Laravel validation):
property1:setting1:setting2...|property2:setting...

Common to all Elements
    size = regular|medium|small|large

Form

    layout = vertical|horizontal|inline
    layout = horizontal[:heading-weight:input-weight[:total-weight]]
    layout = (inline|vertical)[:input-weight[:total-weight]]

    size = regular|medium|small|large

ButtonElement

purpose = primary|secondary|success|danger|warning|info|light|dark|link
fill = solid|outline

FieldElement

Button (same as ButtonElement)

Check (for check boxes and radio options)
    appearance = default|button|button-group (can't be multiple)|no-label
    layout = inline|vertical