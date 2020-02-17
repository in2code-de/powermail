# Radio Buttons

## What does it do?

- **General:** Radiobuttons are made for a single select of an options in the same layout as checkboxes.
- **Mandatory:** This field could be marked as mandatory, so the user must fill min 1 radiobutton, otherwise the form can not be submitted.
- **Prefill:** The field can be preselected from FlexForm, TypoScript, GET/Post-Params or from FE_User table.
- **Special:** Options could also filled by TypoScript in powermail 2.1 and higher (static or dynamic)

## Frontend Output Example

![example_field_radio](../Images/example_field_radio.png)

## Backend Configuration Example

![record_field_radio_tab1](../Images/record_field_radio_tab1.png)

![record_field_radio_tab2](../Images/record_field_radio_tab2.png)

## Explanation

| Field | Description | Explanation | Tab |
|------------------------------------|-----------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|
| Title | Add a label for this field. | The label is shown in the frontend near to this field. | General |
| Type | Choose a fieldtype. | See explanation below for a special fieldtype. Different fields are  related to some fieldtypes – not all fields are shown on every type. | General |
| Options | Options to check | Separate each with a new line. **Note: see following table for examples, how to precheck or clean a value** | General |
| Email of sender | Check this if this field contains the email of the sender. | This is needed to set the correct sender-email-address. If there is no  field marked as Senderemail within the current form, powermail will use a  default value for the Senderemail. | General |
| Name of sender | Check this if this field contains the name (or a part of the name) of the sender. | This is needed to set the correct sender-name. If there is no field  marked as Sendername within the current form, powermail will use a  default value for the Sendername. | General |
| Mandatory Field | This field must contain input. | Check this if the field must contain input, otherwise submitting the form is not possible. | Extended |
| Value from logged in Frontend User | Check if field should be filled from the FE_Users table of a logged in fe_user. | This value overwrites a static value, if set. | Extended |
| Create from TypoScript | Fill Options from TypoScript | If you want to create your options (see above) from TypoScript, you can use this field. Please split each line in your TypoScript with [\n] | Extended |
| Layout | Choose a layout. | This adds a CSS-Class to the frontend output. Administrator can add, remove or rename some of the entries. | Extended |
| Description | Add a description for this field. | Per default a description will be rendered as title-attribute in the labels in frontend. | Extended |
| Variables – Individual Fieldname | This is a marker of this field. | Use a field variable with {marker} in any RTE or HTML-Template. The marker name is equal in any language. | Extended |
| Add own Variable | Check this, if you want to set your own marker (see row before). | After checking this button, TYPO3 ask you to reload. After a reload, you see a new field for setting an own marker. | Extended |
| Language | Choose a language. | Choose in which frontend language this record should be rendered. | Access |
| Hide | Disable the form | Enable or disable this record. | Access |
| Start | Startdate for this record. | Same function as known from default content elements or pages in TYPO3. | Access |
| Stop | Stopdate for this record. | Same function as known from default content elements or pages in TYPO3. | Access |

## Option examples for selectbox

<table>
    <tr>
        <td>Example option</td>
        <td>HTML Output</td>
    </tr>
    <tr>
        <td>Red</td>
        <td>&lt;label&gt;Red&lt;/label&gt;&lt;input type="radio" value=”Red” /&gt;</td>
    </tr>
    <tr>
        <td>Yellow | 1</td>
        <td>&lt;label&gt;Yellow&lt;/label&gt;&lt;input type="radio" value=”1” /&gt;</td>
    </tr>
    <tr>
        <td>Blue |</td>
        <td>&lt;label&gt;Blue&lt;/label&gt;&lt;input type="radio" value=”” /&gt;</td>
    </tr>
    <tr>
        <td>Black Shoes | black | *</td>
        <td>&lt;label&gt;Black Shoes&lt;/label&gt;&lt;input type="radio" value=”black” checked=”checked” /&gt;</td>
    </tr>
    <tr>
        <td>White | | *</td>
        <td>&lt;label&gt;White&lt;/label&gt;&lt;input type="radio" value=”” checked=”checked” /&gt;</td>
    </tr>
    <tr>
        <td>
            Red Shoes | red | *<br>
            Yellow Shoes | yellow | *
        </td>
        <td>&lt;label&gt;Red Shoes&lt;/label&gt;&lt;input type="radio" value=”red” checked=”checked” /&gt;<br>&lt;label&gt;Yellow Shoes&lt;/label&gt;&lt;input type="radio" value=”yellow” checked=”checked” /&gt;</td>
    </tr>
</table>
