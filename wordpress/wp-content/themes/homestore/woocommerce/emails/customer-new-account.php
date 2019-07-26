<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
<meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
<meta content="width=device-width" name="viewport"/>
<!--[if !mso]><!-->
<meta content="IE=edge" http-equiv="X-UA-Compatible"/>
<!--<![endif]-->
<title>Welcome to Elevnn!</title>
<!--[if !mso]><!-->
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css"/>
<!--<![endif]-->
<style type="text/css">
    body {
      margin: 0;
      padding: 0;
    }

    table,
    td,
    tr {
      vertical-align: top;
      border-collapse: collapse;
    }

    * {
      line-height: inherit;
    }

    a[x-apple-data-detectors=true] {
      color: inherit !important;
      text-decoration: none !important;
    }

    .ie-browser table {
      table-layout: fixed;
    }

    [owa] .img-container div,
    [owa] .img-container button {
      display: block !important;
    }

    [owa] .fullwidth button {
      width: 100% !important;
    }

    [owa] .block-grid .col {
      display: table-cell;
      float: none !important;
      vertical-align: top;
    }

    .ie-browser .block-grid,
    .ie-browser .num12,
    [owa] .num12,
    [owa] .block-grid {
      width: 600px !important;
    }

    .ie-browser .mixed-two-up .num4,
    [owa] .mixed-two-up .num4 {
      width: 200px !important;
    }

    .ie-browser .mixed-two-up .num8,
    [owa] .mixed-two-up .num8 {
      width: 400px !important;
    }

    .ie-browser .block-grid.two-up .col,
    [owa] .block-grid.two-up .col {
      width: 300px !important;
    }

    .ie-browser .block-grid.three-up .col,
    [owa] .block-grid.three-up .col {
      width: 300px !important;
    }

    .ie-browser .block-grid.four-up .col [owa] .block-grid.four-up .col {
      width: 150px !important;
    }

    .ie-browser .block-grid.five-up .col [owa] .block-grid.five-up .col {
      width: 120px !important;
    }

    .ie-browser .block-grid.six-up .col,
    [owa] .block-grid.six-up .col {
      width: 100px !important;
    }

    .ie-browser .block-grid.seven-up .col,
    [owa] .block-grid.seven-up .col {
      width: 85px !important;
    }

    .ie-browser .block-grid.eight-up .col,
    [owa] .block-grid.eight-up .col {
      width: 75px !important;
    }

    .ie-browser .block-grid.nine-up .col,
    [owa] .block-grid.nine-up .col {
      width: 66px !important;
    }

    .ie-browser .block-grid.ten-up .col,
    [owa] .block-grid.ten-up .col {
      width: 60px !important;
    }

    .ie-browser .block-grid.eleven-up .col,
    [owa] .block-grid.eleven-up .col {
      width: 54px !important;
    }

    .ie-browser .block-grid.twelve-up .col,
    [owa] .block-grid.twelve-up .col {
      width: 50px !important;
    }
  </style>
<style id="media-query" type="text/css">
    @media only screen and (min-width: 620px) {
      .block-grid {
        width: 600px !important;
      }

      .block-grid .col {
        vertical-align: top;
      }

      .block-grid .col.num12 {
        width: 600px !important;
      }

      .block-grid.mixed-two-up .col.num3 {
        width: 150px !important;
      }

      .block-grid.mixed-two-up .col.num4 {
        width: 200px !important;
      }

      .block-grid.mixed-two-up .col.num8 {
        width: 400px !important;
      }

      .block-grid.mixed-two-up .col.num9 {
        width: 450px !important;
      }

      .block-grid.two-up .col {
        width: 300px !important;
      }

      .block-grid.three-up .col {
        width: 200px !important;
      }

      .block-grid.four-up .col {
        width: 150px !important;
      }

      .block-grid.five-up .col {
        width: 120px !important;
      }

      .block-grid.six-up .col {
        width: 100px !important;
      }

      .block-grid.seven-up .col {
        width: 85px !important;
      }

      .block-grid.eight-up .col {
        width: 75px !important;
      }

      .block-grid.nine-up .col {
        width: 66px !important;
      }

      .block-grid.ten-up .col {
        width: 60px !important;
      }

      .block-grid.eleven-up .col {
        width: 54px !important;
      }

      .block-grid.twelve-up .col {
        width: 50px !important;
      }
    }

    @media (max-width: 620px) {

      .block-grid,
      .col {
        min-width: 320px !important;
        max-width: 100% !important;
        display: block !important;
      }

      .block-grid {
        width: 100% !important;
      }

      .col {
        width: 100% !important;
      }

      .col>div {
        margin: 0 auto;
      }

      img.fullwidth,
      img.fullwidthOnMobile {
        max-width: 100% !important;
      }

      .no-stack .col {
        min-width: 0 !important;
        display: table-cell !important;
      }

      .no-stack.two-up .col {
        width: 50% !important;
      }

      .no-stack .col.num4 {
        width: 33% !important;
      }

      .no-stack .col.num8 {
        width: 66% !important;
      }

      .no-stack .col.num4 {
        width: 33% !important;
      }

      .no-stack .col.num3 {
        width: 25% !important;
      }

      .no-stack .col.num6 {
        width: 50% !important;
      }

      .no-stack .col.num9 {
        width: 75% !important;
      }

      .video-block {
        max-width: none !important;
      }

      .mobile_hide {
        min-height: 0px;
        max-height: 0px;
        max-width: 0px;
        display: none;
        overflow: hidden;
        font-size: 0px;
      }

      .desktop_hide {
        display: block !important;
        max-height: none !important;
      }
    }
  </style>
</head>
<body class="clean-body" style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background-color: #F5F5F5;">
<style id="media-query-bodytag" type="text/css">
@media (max-width: 620px) {
  .block-grid {
    min-width: 320px!important;
    max-width: 100%!important;
    width: 100%!important;
    display: block!important;
  }
  .col {
    min-width: 320px!important;
    max-width: 100%!important;
    width: 100%!important;
    display: block!important;
  }
  .col > div {
    margin: 0 auto;
  }
  img.fullwidth {
    max-width: 100%!important;
    height: auto!important;
  }
  img.fullwidthOnMobile {
    max-width: 100%!important;
    height: auto!important;
  }
  .no-stack .col {
    min-width: 0!important;
    display: table-cell!important;
  }
  .no-stack.two-up .col {
    width: 50%!important;
  }
  .no-stack.mixed-two-up .col.num4 {
    width: 33%!important;
  }
  .no-stack.mixed-two-up .col.num8 {
    width: 66%!important;
  }
  .no-stack.three-up .col.num4 {
    width: 33%!important
  }
  .no-stack.four-up .col.num3 {
    width: 25%!important
  }
}
</style>
<!--[if IE]><div class="ie-browser"><![endif]-->
<table bgcolor="#F5F5F5" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="table-layout: fixed; vertical-align: top; min-width: 320px; Margin: 0 auto; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #F5F5F5; width: 100%;" valign="top" width="100%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td style="word-break: break-word; vertical-align: top; border-collapse: collapse;" valign="top">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color:#F5F5F5"><![endif]-->
<div style="background-color:transparent;">
<div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:transparent;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
<div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top;;">
<div style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->

<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid two-up no-stack" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #FFFFFF;;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:#FFFFFF;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:#FFFFFF"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="300" style="background-color:#FFFFFF;width:300px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 25px; padding-top:25px; padding-bottom:25px;"><![endif]-->
<div class="col num6" style="max-width: 320px; min-width: 300px; display: table-cell; vertical-align: top;;">
<div style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:25px; padding-bottom:25px; padding-right: 0px; padding-left: 25px;">
<!--<![endif]-->
<div align="left" class="img-container left fixedwidth" style="padding-right: 0px;padding-left: 0px;">
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr style="line-height:0px"><td style="padding-right: 0px;padding-left: 0px;" align="left"><![endif]--><img alt="Elevnn Logo" border="0" class="left fixedwidth" src="https://marketing-image-production.s3.amazonaws.com/uploads/af85f989f0e2759655374ca0e02d51bb41dbe014c6304a532a21d9f20764decb46c8671bf7ddd25a4a0d6ed082779dab8aee99a794beed738618a6a74e415c02.png" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; border: 0; height: auto; float: none; width: 100%; max-width: 275px; display: block;" title="Elevnn Logo" width="275"/>
<!--[if mso]></td></tr></table><![endif]-->
</div>
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td><td align="center" width="300" style="background-color:#FFFFFF;width:300px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 25px; padding-left: 0px; padding-top:25px; padding-bottom:25px;"><![endif]-->
<div class="col num6" style="max-width: 320px; min-width: 300px; display: table-cell; vertical-align: top;;">
<div style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:10px; padding-bottom:10px; padding-right: 25px; padding-left: 0px;">
<!--<![endif]-->
<div align="right" class="button-container" style="padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:10px;">
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-spacing: 0; border-collapse: collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"><tr><td style="padding-top: 10px; padding-right: 0px; padding-bottom: 10px; padding-left: 10px" align="right"><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="https://elevnn.com/my-account?utm_source=sendgrid&amp;utm_medium=email&amp;utm_campaign=top_btn_welcome" style="height:31.5pt; width:111.75pt; v-text-anchor:middle;" arcsize="20%" stroke="false" fillcolor="#ffbb00"><w:anchorlock/><v:textbox inset="0,0,0,0"><center style="color:#ffffff; font-family:Arial, sans-serif; font-size:14px"><![endif]--><a href="https://elevnn.com/my-account?utm_source=sendgrid&amp;utm_medium=email&amp;utm_campaign=top_btn_welcome" style="-webkit-text-size-adjust: none; text-decoration: none; display: inline-block; color: #ffffff; background-color: #ffbb00; border-radius: 8px; -webkit-border-radius: 8px; -moz-border-radius: 8px; width: auto; width: auto; border-top: 1px solid #ffbb00; border-right: 1px solid #ffbb00; border-bottom: 1px solid #ffbb00; border-left: 1px solid #ffbb00; padding-top: 5px; padding-bottom: 5px; font-family: 'Open Sans', Helvetica, Arial, sans-serif; text-align: center; mso-border-alt: none; word-break: keep-all;" target="_blank"><span style="padding-left:10px;padding-right:10px;font-size:14px;display:inline-block;">
<span style="font-size: 16px; line-height: 32px;"><span style="font-size: 14px; line-height: 28px;">MY ACCOUNT</span></span>
</span></a>
<!--[if mso]></center></v:textbox></v:roundrect></td></tr></table><![endif]-->
</div>
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #D6E7F0;;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:#D6E7F0;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:#D6E7F0"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:#D6E7F0;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 25px; padding-left: 25px; padding-top:5px; padding-bottom:60px;"><![endif]-->
<div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top;;">
<div style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:60px; padding-right: 25px; padding-left: 25px;">
<!--<![endif]-->
<div align="center" class="img-container center fixedwidth" style="padding-right: 0px;padding-left: 0px;">
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr style="line-height:0px"><td style="padding-right: 0px;padding-left: 0px;" align="center"><![endif]-->
<div style="font-size:1px;line-height:45px"> </div><img align="center" alt="Shop With Elevnn" border="0" class="center fixedwidth" src="https://marketing-image-production.s3.amazonaws.com/uploads/ef7e94319d716cdafc6b51395d2c92e46fa0c4cd648e9d6dc566908ea1bf54296d531240d88bfea7256469c331cf82303c4a777b67468183ffb289cc3dc5c321.png" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; border: 0; height: auto; float: none; width: 100%; max-width: 495px; display: block;" title="Shop With Elevnn" width="495"/>
<!--[if mso]></td></tr></table><![endif]-->
</div>
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 15px; padding-top: 20px; padding-bottom: 0px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#150604;font-family:'Open Sans', Helvetica, Arial, sans-serif;line-height:150%;padding-top:20px;padding-right:10px;padding-bottom:0px;padding-left:15px;">
<div style="font-size: 12px; line-height: 18px; font-family: 'Open Sans', Helvetica, Arial, sans-serif; color: #150604;">
<p style="font-size: 14px; line-height: 27px; text-align: center; margin: 0;"><span style="font-size: 50px;"><strong><span style="line-height: 75px; font-size: 50px;"><span style="font-size: 38px; line-height: 57px;">WELCOME!</span></span></strong></span><span style="font-size: 18px;"></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:'Open Sans', Helvetica, Arial, sans-serif;line-height:150%;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;">
<div style="font-size: 12px; line-height: 18px; font-family: 'Open Sans', Helvetica, Arial, sans-serif; color: #555555;">
<p style="font-size: 14px; line-height: 27px; text-align: center; margin: 0;"><span style="font-size: 18px; color: #000000;">It’s great to meet you. <br/><br/>We know you care about how the products you buy impact the planet and the people who live here. <br/><br/>We make it easy to shop your values by offering all the best ethical products in one easy place, for a simple and convenient shopping experience you can feel good about. <br/><br/>Now that you’ve created an account on Elevnn you can browse our website for all the ethical products you love, keep up with brands who are making the world a better place, and be the first to hear about new deals and promos!<br/><br/>We can’t wait to change the world with you!<br/></span></p>
<p style="font-size: 14px; line-height: 21px; text-align: center; margin: 0;"> </p>
<p style="font-size: 14px; line-height: 21px; text-align: center; margin: 0;"> </p>
<p style="font-size: 14px; line-height: 21px; text-align: center; margin: 0;"> </p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<div align="center" class="button-container" style="padding-top:20px;padding-right:10px;padding-bottom:10px;padding-left:10px;">
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-spacing: 0; border-collapse: collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"><tr><td style="padding-top: 20px; padding-right: 10px; padding-bottom: 10px; padding-left: 10px" align="center"><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="https://elevnn.com/?utm_source=sendgrid&amp;utm_medium=email&amp;utm_campaign=shop_btn_welcome" style="height:39pt; width:163.5pt; v-text-anchor:middle;" arcsize="29%" stroke="false" fillcolor="#ffbb00"><w:anchorlock/><v:textbox inset="0,0,0,0"><center style="color:#ffffff; font-family:Arial, sans-serif; font-size:16px"><![endif]--><a href="https://elevnn.com/?utm_source=sendgrid&amp;utm_medium=email&amp;utm_campaign=shop_btn_welcome" style="-webkit-text-size-adjust: none; text-decoration: none; display: inline-block; color: #ffffff; background-color: #ffbb00; border-radius: 15px; -webkit-border-radius: 15px; -moz-border-radius: 15px; width: auto; width: auto; border-top: 1px solid #ffbb00; border-right: 1px solid #ffbb00; border-bottom: 1px solid #ffbb00; border-left: 1px solid #ffbb00; padding-top: 10px; padding-bottom: 10px; font-family: 'Open Sans', Helvetica, Arial, sans-serif; text-align: center; mso-border-alt: none; word-break: keep-all;" target="_blank"><span style="padding-left:40px;padding-right:40px;font-size:16px;display:inline-block;">
<span style="font-size: 16px; line-height: 32px;"><strong>START SHOPPING</strong></span>
</span></a>
<!--[if mso]></center></v:textbox></v:roundrect></td></tr></table><![endif]-->
</div>
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<div style="background-color:transparent;">
<div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;;">
<div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
<!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
<!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:transparent;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:20px; padding-bottom:60px;"><![endif]-->
<div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top;;">
<div style="width:100% !important;">
<!--[if (!mso)&(!IE)]><!-->
<div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:20px; padding-bottom:60px; padding-right: 0px; padding-left: 0px;">
<!--<![endif]-->
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:'Open Sans', Helvetica, Arial, sans-serif;line-height:120%;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;">
<div style="font-family: 'Open Sans', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 14px; color: #555555;">
<p style="font-size: 14px; line-height: 21px; text-align: center; margin: 0;"><span style="font-size: 18px;">Let’s hang out more!</span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<table cellpadding="0" cellspacing="0" class="social_icons" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" valign="top" width="100%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td style="word-break: break-word; vertical-align: top; padding-top: 10px; padding-right: 10px; padding-bottom: 10px; padding-left: 10px; border-collapse: collapse;" valign="top">
<table activate="activate" align="center" alignment="alignment" cellpadding="0" cellspacing="0" class="social_table" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: undefined; mso-table-tspace: 0; mso-table-rspace: 0; mso-table-bspace: 0; mso-table-lspace: 0;" to="to" valign="top">
<tbody>
<tr align="center" style="vertical-align: top; display: inline-block; text-align: center;" valign="top">
<td style="word-break: break-word; vertical-align: top; padding-bottom: 5px; padding-right: 8px; padding-left: 8px; border-collapse: collapse;" valign="top"><a href="https://www.facebook.com/elevnnstore" target="_blank"><img alt="Facebook" height="32" src="https://marketing-image-production.s3.amazonaws.com/uploads/9e889f42fef6a8445c5ae29e5829db873e58354aca9f75200dab1632c858b05ceb90a1d9d1f7df0fda70138548765b691e68bf79d095186bd95df9a353f4a803.png" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; height: auto; float: none; border: none; display: block;" title="Facebook" width="32"/></a></td>
<td style="word-break: break-word; vertical-align: top; padding-bottom: 5px; padding-right: 8px; padding-left: 8px; border-collapse: collapse;" valign="top"><a href="https://twitter.com/elevnnstore" target="_blank"><img alt="Twitter" height="32" src="https://marketing-image-production.s3.amazonaws.com/uploads/4712642c1d98577b8c776f8c98273f6882f154448f1113a7cc6e7602fd2be9ef79aecfe239de6bd7910acee4b7fc79ee79301e23c53e8bc47a4450a99a06373f.png" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; height: auto; float: none; border: none; display: block;" title="Twitter" width="32"/></a></td>
<td style="word-break: break-word; vertical-align: top; padding-bottom: 5px; padding-right: 8px; padding-left: 8px; border-collapse: collapse;" valign="top"><a href="https://instagram.com/elevnnstore" target="_blank"><img alt="Instagram" height="32" src="https://marketing-image-production.s3.amazonaws.com/uploads/a999b2970f6d082957cb78638e214e0b5d0f0d25964e3a54c19e6d0f7a643d3ac41144d4f61eb69b15907bdaa3ddf57950a7d256a589baf9b66ed8306ce18ad7.png" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; height: auto; float: none; border: none; display: block;" title="Instagram" width="32"/></a></td>
<td style="word-break: break-word; vertical-align: top; padding-bottom: 5px; padding-right: 8px; padding-left: 8px; border-collapse: collapse;" valign="top"><a href="https://www.pinterest.com/elevnnstore" target="_blank"><img alt="Pinterest" height="32" src="https://marketing-image-production.s3.amazonaws.com/uploads/01f3f9a7a10e2154a6fc417f07679eb325bc835f8d3b477f2f8137ccd4fb01199feeedef2ba476bb83053990ade5d2cf6806d510ab9689d489cc8b7b487e5745.png" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; clear: both; height: auto; float: none; border: none; display: block;" title="Pinterest" width="32"/></a></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#555555;font-family:'Open Sans', Helvetica, Arial, sans-serif;line-height:150%;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;">
<div style="font-size: 12px; line-height: 18px; font-family: 'Open Sans', Helvetica, Arial, sans-serif; color: #555555;">
<p style="font-size: 14px; line-height: 18px; text-align: center; margin: 0;"><span style="font-size: 12px;">You are receiving this email as a confirmation of creating an account with Elevnn.com. If you did not create this account please contact <a href="mailto:help@elevnn.com" style="text-decoration: underline; color: #555555;" title="help@elevnn.com">help@elevnn.com</a></span></p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" width="100%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 10px; padding-right: 10px; padding-bottom: 10px; padding-left: 10px; border-collapse: collapse;" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" height="0" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 60%; border-top: 1px dotted #C4C4C4; height: 0px;" valign="top" width="60%">
<tbody>
<tr style="vertical-align: top;" valign="top">
<td height="0" style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse;" valign="top"><span></span></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif"><![endif]-->
<div style="color:#4F4F4F;font-family:'Open Sans', Helvetica, Arial, sans-serif;line-height:120%;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;">
<div style="font-size: 12px; line-height: 14px; font-family: 'Open Sans', Helvetica, Arial, sans-serif; color: #4F4F4F;">
<p style="font-size: 12px; line-height: 14px; text-align: center; margin: 0;"><a href="[Unsubscribe]" rel="noopener" style="color: #ffbb00;" target="_blank">Unsubscribe</a> </p>
</div>
</div>
<!--[if mso]></td></tr></table><![endif]-->
<!--[if (!mso)&(!IE)]><!-->
</div>
<!--<![endif]-->
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
<!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
</div>
</div>
</div>
<!--[if (mso)|(IE)]></td></tr></table><![endif]-->
</td>
</tr>
</tbody>
</table>
<!--[if (IE)]></div><![endif]-->
</body>
</html>

<?php
;