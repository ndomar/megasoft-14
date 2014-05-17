package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.view.Menu;
import android.view.View;
import android.widget.ToggleButton;

/*
 * This activity is responsible for the preferences
 */
public class PreferencesActivity extends Activity {

	SharedPreferences.Editor sharedPreferencesEditor;
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		 sharedPreferencesEditor = getSharedPreferences(
				"preferences", Activity.MODE_PRIVATE).edit();
		super.onCreate(savedInstanceState);
		setContentView(R.layout.template_preferences);
		getActionBar().hide();

	}

	/*
	 * This method gets fired once the push notifications toggle button is
	 * clicked, it sets the text, color and value of the button .
	 */
	public void togglePushNotifications(View v) {
		ToggleButton pushNotifications = (ToggleButton) findViewById(R.id.pushNotificationsToggle);
		if (pushNotifications.getText().toString().equals("ON")) {
			pushNotifications.setTextColor(getResources().getColor(
					R.color.green));
			pushNotifications.setChecked(true);
		} else {
			pushNotifications
					.setTextColor(getResources().getColor(R.color.red));
			pushNotifications.setChecked(false);
		}
		sharedPreferencesEditor.putBoolean("pushNotifications",
				pushNotifications.isChecked());
	}

	/*
	 * This method gets fired once the application sounds toggle button is
	 * clicked, it turns it on or off .
	 */

	public void toggleSounds(View v) {

		ToggleButton sounds = (ToggleButton) findViewById(R.id.soundsToggle);
		if (sounds.getText().toString().equals("ON")) {
			sounds.setTextColor(getResources().getColor(R.color.green));
			sounds.setChecked(true);
		} else {
			sounds.setTextColor(getResources().getColor(R.color.red));
			sounds.setChecked(false);
		}
		sharedPreferencesEditor.putBoolean("sounds", sounds.isChecked());
	}

	/*
	 * This method is fired when the cancel button is clicked, it cancels any
	 * changes that were done by the user .
	 */
	public void cancelChanges(View v) {
		finish();
	}

	/*
	 * This method is fired when the done button is clicked, it saves any
	 * changes that were done by the user .
	 */
	public void changesDone(View v) {
		sharedPreferencesEditor.commit();
		finish();
	}

}
