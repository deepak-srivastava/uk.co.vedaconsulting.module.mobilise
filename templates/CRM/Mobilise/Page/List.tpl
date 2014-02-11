{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright (C) 2011 Marty Wright                                    |
 | Licensed to CiviCRM under the Academic Free License version 3.0.   |
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
{* this template is for listing mobilisations*}

{strip}
{*include file="CRM/common/jsortable.tpl"*}
<legend>Careers Events</legend>
<table class="display">
  <thead>
    <tr id="options" class="columnheader">
      <th >{ts}Date{/ts}</th>
      <th >{ts}Event{/ts}</th>
      <th >{ts}Staff Contact{/ts}</th>
      <th >{ts}Session Focus{/ts}</th>
      <th >{ts}Alumni{/ts}</th>
      <th >{ts}Student{/ts}</th>
      <th >{ts}Note{/ts}</th>
      <th ></th>
    </tr>
  </thead>
  {foreach from=$rows item=row}
    {if $row.mobilisation eq 'Careers'}
      <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"}">
	<td >{$row.date|crmDate}</td>
	<td >{$row.title}</td>
	<td >{$row.staff}</td>
	<td >{$row.session}</td>
	<td >{$row.alumni}</td>
	<td >{$row.student}</td>
	<td >{$row.notes}</td>
	<td>{$row.action}</td>
      </tr>
    {/if}
  {/foreach}
</table>

<legend>Mentor Events</legend>
<table class="display">
  <thead>
    <tr id="options" class="columnheader">
      <th >{ts}Date Range{/ts}</th>
      <th >{ts}Student{/ts}</th>
      <th >{ts}Alumni{/ts}</th>
      <th >{ts}Staff{/ts}</th>
      <th >{ts}Note{/ts}</th>
      <th ></th>
    </tr>
  </thead>
  {foreach from=$rows item=row}
    {if $row.mobilisation eq 'Mentor'}
      <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"}">
	<td >{$row.date|crmDate}</td>
	<td >{$row.student}</td>
	<td >{$row.alumni}</td>
	<td >{$row.staff}</td>
	<td >{$row.notes}</td>
	<td>{$row.action}</td>
      </tr>
    {/if}
  {/foreach}
</table>

<legend>Work Experience Events</legend>
<table class="display">
  <thead>
    <tr id="options" class="columnheader">
      <th >{ts}Date Range{/ts}</th>
      <th >{ts}Student{/ts}</th>
      <th >{ts}Alumni{/ts}</th>
      <th >{ts}Staff{/ts}</th>
      <th >{ts}Note{/ts}</th>
      <th ></th>
    </tr>
  </thead>
  {foreach from=$rows item=row}
    {if $row.mobilisation eq 'Work Experience'}
      <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"}">
	<td >{$row.date|crmDate}</td>
	<td >{$row.student}</td>
	<td >{$row.alumni}</td>
	<td >{$row.staff}</td>
	<td >{$row.notes}</td>
	<td>{$row.action}</td>
      </tr>
    {/if}
  {/foreach}
</table>

<legend>Donation</legend>
<table class="display">
  <thead>
    <tr id="options" class="columnheader">
      <th >{ts}Date Given{/ts}</th>
      <th >{ts}Amount{/ts}</th>
      <th >{ts}Purpose{/ts}</th>
      <th >{ts}Alumni{/ts}</th>
      <th >{ts}Note{/ts}</th>
      <th ></th>
    </tr>
  </thead>
  {foreach from=$rows item=row}
    {if $row.mobilisation eq 'Donation'}
      <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"}">
	<td >{$row.date|crmDate}</td>
	<td >{$row.amount}</td>
	<td >{$row.purpose}</td>
	<td >{$row.alumni}</td>
	<td >{$row.notes}</td>
	<td>{$row.action}</td>
      </tr>
    {/if}
  {/foreach}
</table>

<legend>Governer</legend>
<table class="display">
  <thead>
    <tr id="options" class="columnheader">
      <th >{ts}Date Given{/ts}</th>
      <th >{ts}Amount{/ts}</th>
      <th >{ts}Purpose{/ts}</th>
      <th >{ts}Alumni{/ts}</th>
      <th >{ts}Note{/ts}</th>
      <th ></th>
    </tr>
  </thead>
  {foreach from=$rows item=row}
    {if $row.mobilisation eq 'Governer'}
      <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"}">
	<td >{$row.date|crmDate}</td>
	<td >{$row.amount}</td>
	<td >{$row.purpose}</td>
	<td >{$row.alumni}</td>
	<td >{$row.notes}</td>
	<td>{$row.action}</td>
      </tr>
    {/if}
  {/foreach}
</table>

<legend>Non Careers</legend>
<table class="display">
  <thead>
    <tr id="options" class="columnheader">
      <th >{ts}Date Given{/ts}</th>
      <th >{ts}Amount{/ts}</th>
      <th >{ts}Purpose{/ts}</th>
      <th >{ts}Staff{/ts}</th>
      <th >{ts}Alumni{/ts}</th>
      <th >{ts}Note{/ts}</th>
      <th ></th>
    </tr>
  </thead>
  {foreach from=$rows item=row}
    {if $row.mobilisation eq 'Non Careers'}
      <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"}">
	<td >{$row.date|crmDate}</td>
	<td >0.00</td>
	<td >purpose</td>
	<td >staff..</td>
	<td >{$row.alumni}</td>
	<td >{$row.notes}</td>
	<td>{$row.action}</td>
      </tr>
    {/if}
  {/foreach}
</table>

<legend>Other</legend>
<table class="display">
  <thead>
    <tr id="options" class="columnheader">
      <th >{ts}Date Given{/ts}</th>
      <th >{ts}Amount{/ts}</th>
      <th >{ts}Purpose{/ts}</th>
      <th >{ts}Alumni{/ts}</th>
      <th >{ts}Note{/ts}</th>
      <th ></th>
    </tr>
  </thead>
  {foreach from=$rows item=row}
    {if $row.mobilisation eq 'Other'}
      <tr id="row_{$row.id}" class="{cycle values="odd-row,even-row"}">
	<td >{$row.date|crmDate}</td>
	<td >{$row.amount}</td>
	<td >{$row.purpose}</td>
	<td >{$row.alumni}</td>
	<td >{$row.notes}</td>
	<td>{$row.action}</td>
      </tr>
    {/if}
  {/foreach}
</table>

{/strip}


