{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
<div class="crm-block crm-form-block crm-mailing-group-form-block">
{include file="CRM/common/WizardHeader.tpl"}

  <div class="crm-accordion-wrapper crm-accordion_title-accordion crm-accordion-open">
    <div class="crm-accordion-header">
      <div class="icon crm-accordion-pointer"></div> 
      {ts}New Event{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">

      <table class="form-layout-compressed">
	{if array_key_exists('type', $event_fields)}
	<tr class="crm-mobilise-group-type-block-event_type_id">
	  <td class="label">{$form.event_type_id.label}</td>
	  <td>{$form.event_type_id.html}</td>
	</tr>
	{/if}
	{if in_array('name', $event_fields)}
	<tr class="crm-mobilise-group-type-block-title">
	  <td class="label">{$form.title.label}</td>
	  <td>{$form.title.html}<br />
	    <span class="description"> {ts}Please use only alphanumeric, spaces, hyphens and dashes for event names.{/ts} 
	    </span>
	  </td>
	</tr>
	{/if}
	{if in_array('start_date', $event_fields)}
	<tr class="crm-mobilise-group-type-block-start_date">
	  <td class="label">{$form.start_date.label}</td>
	  <td>{include file="CRM/common/jcalendar.tpl" elementName=start_date}</td>
	</tr>
	{/if}
	{if in_array('end_date', $event_fields)}
	<tr class="crm-mobilise-group-type-block-end_date">
	  <td class="label">{$form.end_date.label}</td>
	  <td>{include file="CRM/common/jcalendar.tpl" elementName=end_date}</td>
	</tr>
	{/if}
      </table>
      {if array_key_exists('custom', $event_fields)}
        {include file="CRM/Override/Form/CustomData.tpl"} 
      {/if}

    </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->

<div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>
</div>
