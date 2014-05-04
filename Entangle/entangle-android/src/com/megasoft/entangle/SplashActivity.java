package com.megasoft.entangle;

import com.megasoft.config.Config;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;

public class SplashActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_splash);

		if (getSharedPreferences(Config.SETTING, 0).getString(
				Config.SESSION_ID, null) != null) {
			Intent registerActivity = new Intent(this, HomeActivity.class);
			startActivity(registerActivity);
		}
	}
}
