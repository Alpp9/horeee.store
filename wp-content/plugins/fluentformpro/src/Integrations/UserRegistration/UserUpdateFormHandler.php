<?php

namespace FluentFormPro\Integrations\UserRegistration;

use FluentForm\App\Helpers\Helper;
use FluentForm\Framework\Helpers\ArrayHelper;


class UserUpdateFormHandler
{
    use Getter;

    public function maybePopulateUserUpdateForm($form)
    {
        $feeds = $this->getFormUserFeeds($form);
        if (!$feeds) {
            return $form;
        }
        foreach ($feeds as $feed) {
            $feed->value = json_decode($feed->value, true);
            if (
                ArrayHelper::isTrue($feed->value, 'enabled') &&
                ArrayHelper::get($feed->value, 'list_id') === 'user_update'
            ) {
                return $this->populateUserUpdateForm($form, $feed->value);
            }
        }

        return $form;
    }

    protected function populateUserUpdateForm($form, $feed)
    {
        if (!get_current_user_id()) {
           return $form;
        }
        $populateFieldsKeys = $this->getFilterUserUpdateFieldsKey($feed);
        $formFields = $form->fields['fields'];
        $populateFields = [];
        foreach ($formFields as $index => $formField) {
            foreach ($populateFieldsKeys as $key => $value) {
                if (
                    ArrayHelper::get($formField, 'element') === 'input_name' &&
                    ArrayHelper::has($formField['fields'], $value)
                ) {
                    $subValue = 'fields.' . $value;
                    $subFiled = ArrayHelper::get($formField, $subValue);
                    if (
                        $subFiled &&
                        ArrayHelper::get($subFiled,'attributes.name') === $value
                    ) {
                        $subFiled['attributes']['value'] = $this->getUserMetaValue($key);
                        $subFiled['attributes']['data-user-update-key'] = $key;
                        if ($key === 'username') {
                            $subFiled['attributes']['readonly'] = true;
                            $subFiled['attributes']['disable'] = true;
                        }
                        $formField['fields'][$value] = $subFiled;
                    }
                }

                if (ArrayHelper::get($formField, 'attributes.name') === $value) {
                    $formField['attributes']['value'] = $this->getUserMetaValue($key);
                    $formField['attributes']['data-user-update-key'] = $key;
                    if ($key === 'username') {
                        $formField['attributes']['readonly'] = true;
                        $formField['attributes']['disable'] = true;
                    }
                }
            }
            $populateFields[$index] = $formField;
        }
        $form->fields['fields'] = $populateFields;
        return $form;
    }

    public function validateSubmittedForm($errors, $data, $form)
    {
        $feeds = $this->getFormUserFeeds($form);

        if (!$feeds) {
            return $errors;
        }

        foreach ($feeds as $feed) {
            $feed->value = json_decode($feed->value, true);
            if (
                $feed->value &&
                ArrayHelper::isTrue($feed->value, 'enabled') &&
                ArrayHelper::get($feed->value, 'list_id') === 'user_update'
            ) {
                $isConditionMatched = $this->checkCondition($feed->value, $data);
                if (!$isConditionMatched) {
                    continue;
                }

                $updateFields = $this->getFilterUserUpdateFieldsKey($feed->value);
                if ($errors = $this->handleUsernameErrors($errors, $data, $updateFields)){
                    return $errors;
                }

                if ($errors = $this->handleEmailErrors($errors, $data, $updateFields)){
                    return $errors;
                }

                if (
                    ArrayHelper::get($updateFields, 'password') &&
                    $errors =  $this->handlePasswordErrors($errors, $data, $updateFields)
                ) {
                   return $errors;
                }
            }
        }

        return $errors;
    }

    protected function getUserMetaValue($key) {
        $userId = get_current_user_id();
        $profileUser = get_userdata($userId);

        switch ($key) {
            case 'username' :
                return $profileUser->user_login;
            case 'email':
                return $profileUser->user_email;
            case 'first_name':
                return $profileUser->first_name;
            case 'last_name':
                return $profileUser->last_name;
            case 'nickname':
                return $profileUser->nickname;
            case 'user_url':
                return $profileUser->user_url;
            case 'description':
                return $profileUser->description;
            default:
                return '';
        }
    }

    protected function getFilterUserUpdateFieldsKey($feed) {
        $keys = $this->supportedFieldsKey();
        $formatFields = [];
        foreach ($keys as $key) {
            $value = ArrayHelper::get($feed, $key);
            if ($value) {
                $formFieldName = '';
                preg_match('/{+(.*?)}/', $value, $matches);

                if (count($matches) > 1) {
                    $matches = explode('.', $matches[1]);
                    $formFieldName = $matches[count($matches) - 1];
                }

                $formatFields[$key] = $formFieldName;
            }
        }
        return $formatFields;
    }

    public function supportedFieldsKey ()
    {
        $supportedFields =  $this->userUpdateMapFields();
        $fieldsKey = [];
        foreach ($supportedFields as $supportedField) {
            $fieldsKey[] = $supportedField['key'];
        }
        return $fieldsKey;
    }

    public function userUpdateMapFields()
    {
        $fields = [
            [
                'key'       => 'username',
                'label'     => 'Username',
                'required'  => true,
                'help_text' => 'simple text field for username reference.'
            ],
            [
                'key' => 'email',
                'label' => 'Email Address',
                'required'  => true,
                'help_text' => 'email reference field',
            ],
            [
                'key'   => 'first_name',
                'label' => 'First Name',
                'help_text'  => 'first name reference field',
            ],
            [
                'key'   => 'last_name',
                'label' => 'Last Name',
                'help_text'  => 'last name reference field',
            ],
            [
                'key'  => 'nickname',
                'label'  => 'Nickname',
                'help_text'  => 'nickname reference field',
            ],
            [
                'key'  => 'user_url',
                'label'  => 'Website Url',
                'help_text'  => 'website reference field',
            ],
            [
                'key'  => 'description',
                'label'  => 'Biographical Info',
                'help_text'  => 'description reference field',
            ],
            [
                'key'       => 'password',
                'label'     => 'Password',
                'help_text' => 'password reference field',
            ],
            [
                'key' => 'repeat_password',
                'label'     => 'Repeat Password',
                'help_text' => 'repeat password reference field',
            ]
        ];
        return apply_filters('fluentform_user_update_map_fields', $fields);
    }

    protected function getDataValueByKey($data, $fields, $key) {
        if (ArrayHelper::get($fields, $key)) {
            $value = ArrayHelper::get($data, $fields[$key]);
            if ($value) {
                return $value;
            }
        }
        if ('username' === $key) {
            return $this->getUserMetaValue($key);
        }
        return '';
    }

    protected function handleEmailErrors($errors = [], $data = [], $fields = [])
    {
        $email = $this->getDataValueByKey($data, $fields, 'email');
        if (!$email) {
            return $this->resetErrormessage($errors, 'Email is required. Please provide an email');
        }

        if ($email != $this->getUserMetaValue('email') && email_exists($email)) {
            return $this->resetErrormessage($errors, 'This email is already registered. Please choose another one.');
        }
        return $errors;
    }

    protected function handleUsernameErrors($errors = [], $data = [], $fields = [])
    {
        $username = $this->getDataValueByKey($data, $fields, 'username');

        if (!$username || $username != $this->getUserMetaValue('username')) {
            return $this->resetErrormessage($errors, 'Username cannot be changed.');
        }
        return $errors;
    }

    protected function handlePasswordErrors($errors, $data, $fields) {
        $password = $this->getDataValueByKey($data, $fields, 'password');

        $confirmPass = $this->getDataValueByKey($data, $fields, 'repeat_password');
        if(
            ArrayHelper::get($fields, 'repeat_password') &&
            $password &&
            $password !== $confirmPass
        ) {
            return $this->resetErrormessage($errors, 'Confirm password not match');
        }
        return $errors;
    }

    public function handleUpdateUser($feed, $formData, $entry, $form, $integrationKey)
    {
        $userId = get_current_user_id();
        if (!$userId) return;
        $parsedData = ArrayHelper::get($feed, 'processedValues');

        $username = $this->getUserMetaValue('username');
        if ($username !== ArrayHelper::get($parsedData, 'username')) {
            return $this->addLog(
                $feed['settings']['name'],
                'failed',
                'username not changeable. try to change username',
                $form->id,
                $entry->id,
                $integrationKey
            );
        };

        $email = ArrayHelper::get($parsedData, 'email');
        if (!$email) return;

        $this->updateUserMeta($parsedData, $userId, $form->id);

        $userId = $this->updateUser($parsedData, $userId);

        if (is_wp_error($userId)) {
            return $this->addLog(
                $feed['settings']['name'],
                'failed',
                $userId->get_error_message(),
                $form->id,
                $entry->id,
                $integrationKey
            );
        }

        Helper::setSubmissionMeta($entry->id, '__updated_user_id', $userId);

        do_action('fluentform_user_update_completed', $userId, $feed, $entry, $form);

        $this->addLog(
            $feed['settings']['name'],
            'success',
            'user has been successfully updated. Updated User ID: ' . $userId,
            $form->id,
            $entry->id,
            $integrationKey
        );

    }

    protected function updateUserMeta($parsedData, $userId, $formId)
    {
        $userMetas = [];
        foreach ($parsedData['userMeta'] as $userMeta) {
            $userMetas[$userMeta['label']] = $userMeta['item_value'];
        }

        foreach ($userMetas as $metaKey => $metaValue) {
            if (trim($metaValue)) {
                update_user_meta($userId, $metaKey, trim($metaValue));
            }
        }

        update_user_meta($userId, 'fluentform_user_id', $formId);
    }
}