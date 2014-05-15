package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.graphics.Color;
import android.view.Menu;
import android.widget.ToggleButton;

public class PreferencesActivity extends Activity {

	private boolean pushToggle = false;
	private boolean emailToggle = false;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.template_preferences);
	}

	public void togglePushNotifications() {
		ToggleButton push = (ToggleButton) findViewById(R.id.pushNotificationsToggle);
		if(pushToggle){
		push.setText("On");
		push.setTextColor(Color.GREEN);
		}
		else{
			push.setText("Off");
			push.setTextColor(Color.RED);
		}
		pushToggle = !pushToggle;
	}

	public void toggleEmailNotifications() {

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
