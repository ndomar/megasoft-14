package com.megasoft.entangle;

import com.megasoft.config.Config;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;

/*
 This class is responsible for creating the Splash activity .
 */
public class SplashActivity extends Activity {

	public static final String REGISTER_LINK = "http://localhost:9001/#register";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_splash);

		getActionBar().hide();

		if (getSharedPreferences(Config.SETTING, 0).getString(
				Config.SESSION_ID, null) != null) {
			Intent registerActivity = new Intent(this, HomeActivity.class);
			startActivity(registerActivity);
			this.finish();
		}
	}

	/**
	 * Redirects to register page
	 * 
	 * @param view
	 * @author eslam
	 */

	public void redirectToRegister(View view) {
		startActivity(new Intent(this, RegisterActivity.class));
		this.finish();
	}

	/**
	 * Redirects to Register in the browser.
	 * 
	 * @param view
	 * @author Farghal
	 */
	public void redirectToLogin(View view) {
		startActivity(new Intent(this, LoginActivity.class));
		this.finish();
	}
}
