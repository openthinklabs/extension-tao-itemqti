<div class="qti-interaction qti-blockInteraction qti-matchInteraction" data-serial="{{serial}}" data-qti-class="matchInteraction">
  {{#if prompt}}{{{prompt}}}{{/if}}
  <div class="match-interaction-area">
    <table class="matrix">
      <thead>
      <tr>
        <th> </th>
        {{#matchSet1}}<th>{{{.}}}</th>{{/matchSet1}}
      </tr>
      </thead>
      <tbody>
      {{#matchSet2}}
      <tr>
        <th>{{{.}}}</th>
        {{#each ../matchSet1}}
        <td>
          <label>
            <input type="checkbox">
            <span class="icon-checkbox cross"></span>
          </label>
        </td>
        {{/each}}
      </tr>
      {{/matchSet2}}
      </tbody>
    </table>
  </div>
  <div class="notification-container"></div>
</div>