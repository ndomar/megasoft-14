package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.graphics.Color;
import android.view.Menu;
import android.view.View;
import android.widget.ToggleButton;

/*
 * This activity is responsible for the preferences
 */
public class PreferencesActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.template_preferences);

	}

	/*
	 * This method gets fired once the push notifications toggle button is
	 * clicked, it sets the text, color and value of the button .
	 */
	public void togglePushNotifications(View v) {
		ToggleButton push = (ToggleButton) findViewById(R.id.pushNotificationsToggle);
		if (push.getText().toString().equals("ON")) {
			push.setTextColor(getResources().getColor(R.color.green));
			push.setChecked(true);
		} else {
			push.setTextColor(getResources().getColor(R.color.red));
			push.setChecked(false);
		}
	}

	/*
	 * This method gets fired once the email notifications toggle button is
	 * clicked, it sets the text, color and value of the button .
	 */

	public void toggleEmailNotifications(View v) {

		ToggleButton email = (ToggleButton) findViewById(R.id.emailNotificationsToggle);
		if (email.getText().toString().equals("ON")) {
			email.setTextColor(getResources().getColor(R.color.green));
			email.setChecked(true);
		} else {
			email.setTextColor(getResources().getColor(R.color.red));
			email.setChecked(false);
		}
	}

	/*
	 * This method is fired when the cancel button is clicked, it cancels any
	 * changes that were done by the user .
	 */
	public void cancelChanges(View v) {

	}

	/*
	 * This method is fired when the done button is clicked, it saves any
	 * changes that were done by the user .
	 */
	public void changesDone(View v) {

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.preferences, menu);
		return true;
	}

}
