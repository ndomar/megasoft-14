package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.graphics.Color;
import android.view.Menu;
import android.view.View;
import android.widget.ToggleButton;

public class PreferencesActivity extends Activity {

	ToggleButton push;
	ToggleButton email;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.template_preferences);
		push = (ToggleButton) findViewById(R.id.pushNotificationsToggle);
		email = (ToggleButton) findViewById(R.id.emailNotificationsToggle);

	}

	public void togglePushNotifications(View v) {
		if (push.getText().equals("OFF")) {
			push.setText("ON");
			push.setTextColor(getResources().getColor(R.color.green));
		} else {
			push.setText("OFF");
			push.setTextColor(getResources().getColor(R.color.red));
		}
	}

	public void toggleEmailNotifications(View v) {
		if (email.getText().equals("OFF")) {
			email.setText("ON");
			email.setTextColor(getResources().getColor(R.color.green));
		} else {
			email.setText("OFF");
			email.setTextColor(getResources().getColor(R.color.red));
		}

	}

	public void cancelChanges() {

	}

	public void changesDone() {

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.preferences, menu);
		return true;
	}

}
