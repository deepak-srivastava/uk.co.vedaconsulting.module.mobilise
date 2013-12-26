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
<div class="crm-block crm-form-block crm-mobilise-group-alumni-block">
{include file="CRM/common/WizardHeader.tpl"}

  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>

  <div class="crm-accordion-wrapper crm-accordion_title-accordion crm-accordion-open">
    <div class="crm-accordion-header">
      <div class="icon crm-accordion-pointer"></div> 
      {ts}Participant Fields{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">

      <table class="form-layout-compressed">
	<tr class="crm-mobilise-group-alumni-block-role_id"><td class="label">{$form.role_id.label}</td><td>{$form.role_id.html}</td></tr>
	<tr class="crm-mobilise-group-alumni-block-register_date">
	  <td class="label">{$form.register_date.label}</td>
	  <td>
	    {if $hideCalendar neq true}
	      {include file="CRM/common/jcalendar.tpl" elementName=register_date}
	    {else}
	      {$form.register_date.html|crmDate}
	    {/if}
	  </td>
	</tr>
	<tr class="crm-mobilise-group-alumni-block-status_id">
	  <td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td>
	</tr>
	<tr class="crm-mobilise-group-alumni-block-source">
	  <td class="label">{$form.source.label}</td><td>{$form.source.html|crmReplace:class:huge}<br />
	    <span class="description">{ts}Source for this registration (if applicable).{/ts}</span></td>
	</tr>
      </table>

    </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->

  <div class="crm-accordion-wrapper crm-accordion_title-accordion crm-accordion-open">
    <div class="crm-accordion-header">
      <div class="icon crm-accordion-pointer"></div> 
      {ts}Selected Contacts{/ts}
    </div><!-- /.crm-accordion-header -->
    <div class="crm-accordion-body">
      
    </div><!-- /.crm-accordion-body -->
  </div><!-- /.crm-accordion-wrapper -->
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl"}</div>
</div>
