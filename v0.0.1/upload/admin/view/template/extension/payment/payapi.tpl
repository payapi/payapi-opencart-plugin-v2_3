<?php echo $header;?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-pp-std-uk" data-toggle="tooltip" title="<?php echo $button_update; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if (isset($error['error_warning'])) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['error_warning']; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php if ($success) { ?>
      <?php } ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <p>&nbsp;</p>
    <div class="col-md-12 profile_details">
      <div class="well profile_view clearfix">
        <div class="col-sm-12">
          <div class="right col-sm-3 text-center">
            <a href="<?=$branding['partnerWebUrl']?>" target="blank"><img alt="<?=$branding['partnerName']?>, <?=$branding['partnerSlogan']?>" src="<?=$branding['partnerLogoUrl']?>" height="44" width="auto" style="margin-top: 1em;"></a>
          </div>
          <div class="left col-sm-9">
            <h1 style="margin-top: 0;"><?=$branding['partnerSlogan']?></h1>
            <p><?=strip_tags(html_entity_decode($branding['partnerSupportInfoL1']),'<strong><a><b>')?></p>
            <?php if(isset($branding['partnerSupportInfoL2']) === true && $branding['partnerSupportInfoL1'] != null) {?>
            <p><?=$branding['partnerSupportInfoL2']?></p>
            <?php }?>
            <p style="margin-bottom: 1em;">&nbsp;</p>
            <div class="text-right" style="margin-bottom: 2em; clear: both;">
              <a href="<?=$branding['partnerWebUrl']?>" class="btn btn-info" target="blank" type="button">
                <i class="fa fa-info"> </i> <?=$branding['partnerName']?>
              </a>
              <?php
              if (isset($branding['partnerContactEmail']) === true && $branding['partnerContactEmail'] != null) {
              ?>
              <a href="mailto: <?=$branding['partnerContactEmail']?>" class="btn btn-info" target="blank" type="button">
                <i class="fa fa-envelope"> </i> <?=$text_email?>
              </a>
              <?php
              }
              if (isset($branding['partnerContactPhone']) === true && $branding['partnerContactPhone'] != null) {
              ?>
              <a href="tel: <?=$branding['partnerContactPhone']?>" class="btn btn-info" target="blank" type="button">
                <i class="fa fa-phone"> </i> <?=$text_phone?>
              </a>
              <?php
              }?>
              <a href="<?=$branding['dashboard']?>" class="btn btn-info" target="blank" type="button" style="background-color: #4298d5;">
                <i class="fa fa-credit-card"> </i> <?=$text_dashboard?>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-pp-std-uk" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-status" data-toggle="tab"><?php echo $tab_status; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="entry-merchant-id"><span data-toggle="tooltip" title="<?php echo $help_public_id; ?>"><?php echo $label_public_id; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="payapi_public_id" value="<?php echo $payapi_public_id; ?>" placeholder="<?php echo $label_public_id; ?>" id="entry-merchant-id" class="form-control" autocomplete="off"/>
                  <?php if ($error_account) { ?>
                  <div class="text-danger"><?php echo $error_account; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="entry-api-key"><span data-toggle="tooltip" title="<?php echo $help_api_key; ?>"><?php echo $label_api_key; ?></label>
                <div class="col-sm-10">
                  <input type="password" name="payapi_api_key" value="<?php echo $payapi_api_key; ?>" placeholder="<?php echo $label_api_key; ?>" id="entry-api-key" class="form-control" autocomplete="off"/>
                  <?php if ($error_account) { ?>
                  <div class="text-danger"><?php echo $error_account; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="entry-default-shipping"><span data-toggle="tooltip" title="<?php echo $help_shipping; ?>"><?php echo $label_shipping; ?></label>
                <div class="col-sm-10">
                  <select name="payapi_shipping"  id="entry-default-shipping" id="entry-default-shipping" class="form-control">
                    <option value ="">--- <?=$select_shipping?> ---</option>
                      <?php
                      foreach ( $shippings as $shipping ) {
                        $selected = null ;
                        if ( $shipping == $payapi_shipping ) $selected = ' selected' ;?>
                    <option value="<?=$shipping?>"<?=$selected?>><?=$shipping?></option>
                      <?php }?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-instantpayments"><span data-toggle="tooltip" title="<?php echo $help_instantpayments; ?>"><?php echo $label_instant_payments; ?></span></label>
                <div class="col-sm-10">
                  <select name="payapi_instantpayments" id="input-instantpayments" class="form-control">
                    <?php if ($payapi_instantpayments) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-mode"><span data-toggle="tooltip"><?php echo $label_mode; ?></span></label>
                <div class="col-sm-10">
                  <select name="payapi_test" id="input-live-demo" class="form-control">
                    <option value="3"<?=$input_selected?>><?=$mode_3?></option>
                    <option value="2"><?=$mode_2?></option>
                    <option value="1"><?=$mode_1?></option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $label_debug; ?></span></label>
                <div class="col-sm-10">
                  <select name="payapi_debug" id="input-debug" class="form-control">
                    <?php if ($payapi_debug) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-status">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-processing-status"><?php echo $label_status_processing; ?></label>
                <div class="col-sm-10">
                  <select name="payapi_status_processing_id" id="input-processing-status" class="form-control">
                    <?php
                    foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $payapi_processing_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-processed-status"><?php echo $label_status_processed; ?></label>
                <div class="col-sm-10">
                  <select name="payapi_status_processed_id" id="input-processed-status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $payapi_processed_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-failed-status"><?php echo $label_status_failed; ?></label>
                <div class="col-sm-10">
                  <select name="payapi_status_failed_id" id="input-failed-status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $payapi_failed_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-chargeback-status"><?php echo $label_status_chargeback; ?></label>
                <div class="col-sm-10">
                  <select name="payapi_status_chargeback_id" id="input-chargeback-status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $payapi_chargeback_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <input type="hidden" name="payapi_order" value="0" id="input-sort-order">
        </form>
        <hr>
        <div class="tab-pane">
          <div class="form-group">
            <label class="col-sm-2 control-label text-right"><span data-toggle="tooltip" title="<?php echo $help_account_status; ?>"><?php echo $label_account_status; ?></span></label>
            <div class="col-sm-10">
              <span class="text-uppercase label-<?=$account_status_class?>" data-toggle="tooltip" title="<?=$account_status_tooltip?>" style="color:#fff;letter-spacing:1px;padding:.4em .8em;">
                <?=$account_status?>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
