<div>
  <h1>Messaging Hub Options Dashboard</h1>
  <form method="POST">
    <div>
      The following parameters are required to define connections to your messaging apps.
    </div>
    <div>
      <h4>HipChat Messaging Channel</h4>
      <div>
        <p>
          <label>Room ID</label>
          <br />
          <input type="text" name="hipchat_room_id" id="hipchat_room_id" value="<?php echo $hipchat_room_id; ?>">
        </p>
      </div>
      <div>
        <p>
          <label>Authentication Token</label>
          <br />
          <input type="text" name="hipchat_auth_token" id="hipchat_auth_token" size="50" value="<?php echo $hipchat_auth_token; ?>">
        </p>
      </div>
      <h4>Slack Messaging Channel</h4>
      <div>
        <p>
          <label>Channel ID</label>
          <br />
          <input type="text" name="slack_channel_id" id="slack_channel_id" value="<?php echo $slack_channel_id; ?>">
        </p>
      </div>
      <div>
        <p>
          <label>Authentication Token</label>
          <br />
          <input type="text" name="slack_auth_token" id="slack_auth_token" size="50" value="<?php echo $slack_auth_token; ?>">
        </p>
      </div>
    </div>
    <?php
    echo wp_nonce_field( 'mhub-options-dashboard' );
    submit_button();
    ?>
  </form>
</div>
