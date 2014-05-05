package com.megasoft.entangle;

import com.megasoft.config.Config;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;

public class SplashActivity extends Activity {

	public static final String REGISTER_LINK = "http://entangle.io/app_dev.php/register";
	
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
	 * Redirects to register in the browser.
	 * @param view
	 * @author Farghal
	 */
	public void redirectToRegister(View view) {
		Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(REGISTER_LINK));
		startActivity(browserIntent);
	}
	
	/**
	 * Redirects to Register in the browser.
	 * @param view
	 * @author Farghal
	 */
	public void redirectToLogin(View view) {
		startActivity(new Intent(this, LoginActivity.class));
		this.finish();
	}
}
