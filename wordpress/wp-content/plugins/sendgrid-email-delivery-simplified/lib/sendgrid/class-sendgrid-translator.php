<?php

require_once plugin_dir_path( __FILE__ ) . '../../vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'sendgrid-api-v3.php';

class Sendgrid_Translator {
  /**
   * Checks if the specified variable holds a non-empty string
   *
   * @param   type  string  $input_string
   *
   * @return  bool
   */
  private static function is_valid_string( $input_string ) {
    if ( is_string( $input_string ) and
      strlen( trim( $input_string ) ) > 0 ) {
      return true;
    }

    return false;
  }

  /**
   * Returns an array of filter settings for the specified filter key from the SMTPAPI header of a v2 Email
   *
   * @param   type  SendGrid\Email    $email_v2
   * @param   type  string            $filter_key
   * @param   type  array             $filter_settings
   * @param   type  string            $filter_enabled
   *
   * @return  array
   */
  private static function get_smtp_filter_settings(
    SendGrid\Email  $email_v2,
                    $filter_key, 
                    $filter_settings,
                    $filter_enabled   = 'enable'
  ) {
    $filter_sub_label = 'settings';
    $output_array     = array();

    if ( ! is_array( $filter_settings ) ) {
      return $output_array;
    }

    // Check that the SMTPAPI header filter object is not malformed
    if ( ! is_array( $email_v2->smtpapi->filters ) ) {
      return $output_array;
    }

    // Check that the filter object exists
    if ( ! isset( $email_v2->smtpapi->filters[ $filter_key ] ) ) {
      return $output_array;
    }

    // Check that 'settings' exist under filter
    if ( ! isset( $email_v2->smtpapi->filters[ $filter_key ][ $filter_sub_label ] ) ) {
      return $output_array;
    }

    // Avoid PHP warning when foreaching for settings by making sure it's an array
    if ( ! is_array( $email_v2->smtpapi->filters[ $filter_key ][ $filter_sub_label ] ) ) {
      return $output_array;
    }

    // Make sure there is an enabled flag
    if ( ! isset( $email_v2->smtpapi->filters[ $filter_key ][ $filter_sub_label ][ $filter_enabled ] ) ) {
      return $output_array;
    }

    // If it's not enabled, return empty array, no need to make the payload bigger
    if ( ! $email_v2->smtpapi->filters[ $filter_key ][ $filter_sub_label ][ $filter_enabled ]  ) {
      return $output_array;
    }

    foreach ( $email_v2->smtpapi->filters[ $filter_key ][ $filter_sub_label ] as $setting_key => $setting_value ) {
      if ( in_array( $setting_key, $filter_settings ) ) {
        $output_array[ $setting_key ] = $setting_value;
      }
    }
    
    return $output_array;
  }

  /**
   * Sets the From address and FromName (if set) to a V3 Email from a V2 Email
   *  - for API V3 the From email address is mandatory and it may not include Unicode encoding
   *  - for API V3 the FromName is optional
   *
   * @param   type  SendGridV3\Mail   $email_v3
   * @param   type  SendGrid\Email    $email_v2
   *
   * @return  void
   */
  private static function set_from_v3(
    SendGridV3\Mail   $email_v3,
    SendGrid\Email    $email_v2
  ) {
    $from_name = null;

    if ( isset( $email_v2->fromName ) and self::is_valid_string( $email_v2->fromName ) ) {
      $from_name = trim( $email_v2->fromName );
    }

    $from = new SendGridV3\Email( $from_name, trim( $email_v2->from ) );
    $email_v3->setFrom( $from );
  }

  /**
   * Sets the Subject (if set) to a V3 Email from a V2 Email
   *  - for API V3 the Subject field is optional
   *
   * @param   type  SendGridV3\Mail   $email_v3
   * @param   type  SendGrid\Email    $email_v2
   *
   * @return  void
   */
  private static function set_subject_v3(
    SendGridV3\Mail   $email_v3,
    SendGrid\Email    $email_v2
  ) {
    if ( isset( $email_v2->subject ) and self::is_valid_string( $email_v2->subject ) ) {
      $email_v3->setSubject( $email_v2->subject );
    }
  }

  /**
   * Sets the plaintext content (if set) to a V3 Email from a V2 Email
   *  - for API V3 at least one content object must be present (either plaintext or html)
   *
   * @param   type  SendGridV3\Mail   $email_v3
   * @param   type  SendGrid\Email    $email_v2
   *
   * @return  void
   */
  private static function set_text_content_v3(
    SendGridV3\Mail   $email_v3,
    SendGrid\Email    $email_v2
  ) {
    if ( isset( $email_v2->text ) and self::is_valid_string( $email_v2->text ) ) {
      $text_content = new SendGridV3\Content( 'text/plain', $email_v2->text );
      $email_v3->addContent($text_content);
    }
  }

  /**
   * Sets the HTML content (if set) to a V3 Email from a V2 Email
   *  - for API V3 at least one content object must be present (either plaintext or html)
   *
   * @param   type  SendGridV3\Mail   $email_v3
   * @param   type  SendGrid\Email    $email_v2
   *
   * @return  void
   */
  private static function set_html_content_v3(
    SendGridV3\Mail   $email_v3,
    SendGrid\Email    $email_v2
  ) {
    if ( isset( $email_v2->html ) and self::is_valid_string( $email_v2->html ) ) {
      $html_content = new SendGridV3\Content( 'text/html', $email_v2->html );
      $email_v3->addContent($html_content);
    }
  }

  /**
   * Sets the To addresses and ToNames (if set) to a V3 Personalization Object from a V2 Email
   *  - for API V3 at least one recipient (To email address) must be present
   *  - for API V3 the To Name is optional
   *  - also adds substitutions, custom args and send each at, if present for each email
   *
   * @param   type  SendGridV3\Mail               $email_v3
   * @param   type  SendGrid\Email                $email_v2
   *
   * @return  void
   */
  private static function set_tos_v3(
    SendGridV3\Mail             $email_v3,
    SendGrid\Email              $email_v2
  ) {
    if ( ! is_array( $email_v2->to ) ) {
      return;
    }

    // Create a new personalization for this To
    $personalization  = new SendGridV3\Personalization();

    foreach ( $email_v2->to as $index => $address ) {
      if ( ! self::is_valid_string( $address ) ) {
        continue;
      }

      $to_name      = null;
      $to_address   = trim( $address );

      if ( isset( $email_v2->toName[ $index ] ) and
        self::is_valid_string( $email_v2->toName[ $index ] ) ) {
        $to_name = trim( $email_v2->toName[ $index ] );
      }

      $recipient = new SendGridV3\Email( $to_name, $to_address );

      // Add the values
      $personalization->addTo( $recipient );
      self::set_substitutions_v3( $index, $personalization, $email_v2 );
      self::set_custom_args_v3( $index, $personalization, $email_v2 );
      self::set_send_each_at_v3( $index, $personalization, $email_v2 );
    }

    // Append the personalization to the email
    $email_v3->addPersonalization( $personalization );
  }

  /**
   * Sets the CC addresses and CCNames (if set) to a V3 Personalization Object from a V2 Email
   *  - for API V3 the CC addresses are optional
   *  - for API V3 the CC Name is optional for all CC addresses
   *
   * @param   type  SendGridV3\Personalization    $personalization
   * @param   type  SendGrid\Email                $email_v2
   *
   * @return  void
   */
  private static function set_ccs_v3(
    SendGridV3\Personalization  $personalization,
    SendGrid\Email              $email_v2
  ) {
    if ( ! is_array( $email_v2->cc ) ) {
      return;
    }

    foreach ( $email_v2->cc as $index => $address ) {
      // Check if "cc name" is set
      $cc_name = null;
      if ( self::is_valid_string( $email_v2->ccName[ $index ] ) ) {
        $cc_name = trim( $email_v2->ccName[ $index ] );
      }

      $recipient = new SendGridV3\Email( $cc_name, $address );
      $personalization->addCc( $recipient );
    }
  }

  /**
   * Sets the BCC addresses and BCCNames (if set) to a V3 Personalization Object from a V2 Email
   *  - for API V3 the BCC addresses are optional
   *  - for API V3 the BCC Name is optional for all BCC addresses
   *
   * @param   type  SendGridV3\Personalization    $personalization
   * @param   type  SendGrid\Email                $email_v2
   *
   * @return  void
   */
  private static function set_bccs_v3(
    SendGridV3\Personalization  $personalization,
    SendGrid\Email              $email_v2
  ) {
    if ( ! is_array( $email_v2->bcc ) ) {
      return;
    }

    foreach ( $email_v2->bcc as $index => $address ) {
      // Check if "bcc name" is set
      $bcc_name = null;
      if ( self::is_valid_string( $email_v2->bccName[ $index ] ) ) {
        $bcc_name = trim( $email_v2->bccName[ $index ] );
      }

      $recipient = new SendGridV3\Email( $bcc_name, $address );
      $personalization->addBcc( $recipient );
    }
  }

  /**
   * Sets the ReplyTo address (if set) to a V3 Email from a V2 Email
   *  - for API V3 the ReplyTo email address is optional and it may not include Unicode encoding
   *
   * @param   type  SendGridV3\Mail   $email_v3
   * @param   type  SendGrid\Email    $email_v2
   *
   * @return  void
   */
  private static function set_reply_to_v3(
    SendGridV3\Mail   $email_v3,
    SendGrid\Email    $email_v2
  ) {
    if ( isset( $email_v2->replyTo ) and self::is_valid_string( $email_v2->replyTo ) ) {
       $email_v3->setReplyTo( new SendGridV3\Email( null, trim( $email_v2->replyTo ) ) );
    }
  }

  /**
   * Sets the Headers (if set) to a V3 Email from a V2 Email
   *  - for API V3 the CC addresses are optional
   *  - for API V3 the CC Name is optional for all CC addresses
   *
   * @param   type  SendGridV3\Mail   $email_v3
   * @param   type  SendGrid\Email    $email_v2
   *
   * @return  void
   */
  private static function set_headers_v3(
    SendGridV3\Mail   $email_v3,
    SendGrid\Email    $email_v2
  ) {
    if ( ! is_array( $email_v2->headers ) ) {
      return;
    }

    foreach ( $email_v2->headers as $header => $value ) {
      $email_v3->addHeader( $header, $value );
    }
  }

  /**
   * Sets the Attachments (if set) to a V3 Email from a V2 Email
   *  - only attaches file if it's present at specified path and readable
   *  - only the content and filename fields are mandatory
   *  - content field must be base64 encoded
   *
   * @param   type  SendGridV3\Mail   $email_v3
   * @param   type  SendGrid\Email    $email_v2
   *
   * @return  void
   */
  private static function set_attachments_v3( 
    SendGridV3\Mail   $email_v3,
    SendGrid\Email    $email_v2
  ) {
    if ( ! is_array( $email_v2->attachments ) ) {
      return;
    }

    foreach ( $email_v2->attachments as $index => $file_info ) {
      if ( ! isset( $file_info[ 'file' ] ) or ! isset( $file_info[ 'basename' ] ) ) {
        continue;
      }

      $file_contents = file_get_contents( $file_info[ 'file' ] );
  
      // file_get_contents retuns a bool or non-bool which evaluates to false if it fails
      if ( ! $file_contents ) {
        continue;
      }

      $file_contents = base64_encode( $file_contents );

      // base64_encode returns a bool or non-bool which evaluates to false if it fails
      if ( ! $file_contents ) {
        continue;
      }

      $attachment = new SendGridV3\Attachment();
      $attachment->setContent( $file_contents );
      $attachment->setFilename( $file_info[ 'basename' ] );

      // Set the custom filename if specified
      if ( isset( $file_info[ 'custom_filename' ] ) and
        self::is_valid_string( $file_info[ 'custom_filename' ] ) ) {
          $attachment->setFilename( trim( $file_info[ 'custom_filename' ] ) );
      }

      // Set the Content ID if specified
      if ( isset( $file_info[ 'cid' ] ) and
        self::is_valid_string( $file_info[ 'cid' ] ) ) {
        $attachment->setContentID( trim( $file_info[ 'cid' ] ) );
      }

      $email_v3->addAttachment( $attachment );
    }
  }

  /**
   * Sets the Substitution (if set) to a V3 Personalization from a V2 Email
   *
   * @param   type  integer                       $index
   * @param   type  SendGridV3\Personalization    $personalization
   * @param   type  SendGrid\Email                $email_v2
   *
   * @return  void
   */
  private static function set_substitutions_v3 (
                                $index,
    SendGridV3\Personalization  $personalization,
    SendGrid\Email              $email_v2
  ) {
    if ( ! is_array( $email_v2->smtpapi->sub ) ) {
      return;
    }

    foreach ( $email_v2->smtpapi->sub as $key => $array_values ) {
      if ( isset( $array_values[ $index ] ) ) {
        $personalization->addSubstitution( $key, $array_values[ $index ] );
      }
    }
  }

  /**
   * Sets the Custom Args (if set) to a V3 Personalization from a V2 Email
   *
   * @param   type  integer                       $index
   * @param   type  SendGridV3\Personalization    $personalization
   * @param   type  SendGrid\Email                $email_v2
   *
   * @return  void
   */
  private static function set_custom_args_v3 (
                                $index,
    SendGridV3\Personalization  $personalization,
    SendGrid\Email              $email_v2
  ) {
    if ( ! is_array( $email_v2->smtpapi->unique_args ) ) {
      return;
    }

    foreach ( $email_v2->smtpapi->unique_args as $key => $array_values ) {
      if ( isset( $array_values[ $index ] ) ) {
        $personalization->addCustomArg( $key, $array_values[ $index ] );
      }
    }
  }

  /**
   * Sets the SendAt for each XSMTPAPI To (if set) to a V3 Personalization from a V2 Email
   * - for API V3 the valus of send_at is a an integer (UNIX Timestamp)
   *
   * @param   type  integer                       $index
   * @param   type  SendGridV3\Personalization    $personalization
   * @param   type  SendGrid\Email                $email_v2
   *
   * @return  void
   */
  private static function set_send_each_at_v3 (
                                $index,
    SendGridV3\Personalization  $personalization,
    SendGrid\Email              $email_v2
  ) {
    if ( ! is_array( $email_v2->smtpapi->send_each_at ) ) {
      return;
    }

    if ( isset( $email_v2->smtpapi->send_each_at[ $index ] ) ) {

      if( is_string( $email_v2->smtpapi->send_each_at[ $index ] ) ) {
        $personalization->setSendAt( intval( trim( $email_v2->smtpapi->send_each_at[ $index ] ) ) );
      } else {
        $personalization->setSendAt( $email_v2->smtpapi->send_each_at[ $index ] );
      }
    }
  }

  /**
   * Sets the SMTPAPI To addresses and ToNames (if set) to a V3 Personalization Object from a V2 Email
   *  - for API V3 at least one recipient (To email address) must be present
   *  - for API V3 the To Name is optional
   *  - SMTPAPI headers have the ToNames in <> brackets, they need to be extracted
   *  - will also set substitution per email
   *  - each SMTPAPI to will have it's own personalization
   *
   * @param   type  SendGridV3\Mail               $email_v3
   * @param   type  SendGrid\Email                $email_v2
   *
   * @return  void
   */
  private static function set_smtpapi_tos_v3 (
    SendGridV3\Mail   $email_v3,
    SendGrid\Email    $email_v2
  ) {
    if ( ! is_array( $email_v2->smtpapi->to ) ) {
      return;
    }

    foreach ( $email_v2->smtpapi->to as $index => $address ) {
      if ( ! self::is_valid_string( $address ) ) {
        continue;
      }

      $to_name      = null;
      $to_address   = trim( $address );

      // If there is a ToName
      if ( strstr( $address, '<' ) ) {
        // Match for any string followed by any string between <> brackets
        preg_match( '/(.*?)<([^>]+)>/', $address, $output_array );

        // 3nd Grouping (position 2 in array) will be the email address
        if ( isset( $output_array[ 2 ] ) ) {
          $to_address = trim( $output_array[ 2 ] );
        }

        // 2rd Grouping (position 1 in array) will be the ToName
        if ( isset( $output_array[ 1 ] ) ) {
          $to_name = trim( $output_array[ 1 ] );
        }
      }

      // If no <> brackets are found, there should only be one email address
      $recipient = ne