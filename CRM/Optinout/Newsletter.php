<?php

class CRM_Optinout_Newsletter {
  public const CustomFieldId_Newsletter = 16;
  public const CustomFieldId_OtherNews = 17;

  public static function processValueChange($field) {
    $contactId = $field['entity_id'];
    $customFieldId = $field['custom_field_id'];
    $oldValue = self::getCustomValue($contactId, $customFieldId);
    $newValue = $field['value'];

    if ($oldValue !== $newValue) {
      self::createOptInOptOutActivity($contactId, $customFieldId, $newValue);
    }
  }

  private static function getCustomValue($contactId, $customFieldId) {
    try {
      $result = civicrm_api3('Contact', 'getsingle', [
        'return' => ["custom_$customFieldId"],
        'id' => $contactId,
      ]);

      return $result["custom_$customFieldId"];
    }
    catch (Exception $e) {
      return '';
    }
  }

  private static function createOptInOptOutActivity($contactId, $customFieldId, $newValue) {
    if ($newValue == 1) {
      self::createActivity('Opt-in', $contactId, $customFieldId);
    }
    else {
      self::createActivity('Opt-out', $contactId, $customFieldId);
    }
  }

  private static function createActivity($activityPrefix, $contactId, $customFieldId) {
    $fieldDescription = self::getFieldDescription($customFieldId);

    try {
      civicrm_api3('Activity', 'create', [
        'source_contact_id' => $contactId,
        'activity_type_id' => "Opt-in / Opt-out",
        'subject' => $activityPrefix . ' ' . $fieldDescription,
        'status_id' => 'Completed',
      ]);
    }
    catch (Exception $e) {
      // ignore errors
    }
  }

  private static function getFieldDescription($customFieldId) {
    // could be dynamic, but for performance and consistency reasons we do it statically
    if ($customFieldId == self::CustomFieldId_Newsletter) {
      return 'PICUM Newsletter';
    }
    else {
      return 'Other News';
    }
  }
}
