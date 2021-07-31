# org.picum.optinout

Creates an activity of type "Opt-in / Opt-out" when the communication yes/no fields change.

![Screenshot](/images/screenshot.png)

The hook optinout_civicrm_customPre checks if the right field is changed.

The static method CRM_Optinout_Newsletter::processValueChange() compares the old and new value and creates an activity if needed.

