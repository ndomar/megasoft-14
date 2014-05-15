package com.megasoft.entangle;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

/**
 * Views a user's general profile given his user Id
 * 
 * @author Almgohar
 */
public class GeneralProfileActivity extends FragmentActivity {
	private static final String LOGOUT = "/user/logout";
	private int userId;
	private int tangleId;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);
		ProfileFragment profile = new ProfileFragment();
		Bundle bundle = new Bundle();
		tangleId = getIntent().getIntExtra("tangleId", -1);
		userId = getIntent().getIntExtra("userId", -1);
		bundle.putInt("tangleId", tangleId);
		bundle.putInt("userId", userId);
		bundle.putBoolean("general", true);
		profile.setArguments(bundle);
		FragmentTransaction transaction = getSupportFragmentManager()
				.beginTransaction();
		transaction.add(R.id.profile_layout, profile);
		transaction.commit();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu items for use in the action bar
		MenuInflater inflater = getMenuInflater();
		inflater.inflate(R.menu.general_profile, menu);
		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle presses on the action bar items
		switch (item.getItemId()) {
		case R.id.logoutButton:
			logout();
			return true;
		default:
			return super.onOptionsItemSelected(item);
		}
	}

	/*
	 * this method gets the sessionId and sends a request to the server and then
	 * calls goToLogout method
	 * 
	 * 
	 * @author maisaraFarahat
	 */

	@SuppressWarnings("deprecation")
	public void logout() {

		SharedPreferences myPrefs = this.getSharedPreferences(Config.SETTING,
				MODE_WORLD_READABLE);
		String sessionId = myPrefs.getString(Config.SESSION_ID, "");
		PostRequest request = new PostRequest(Config.API_BASE_URL_SERVER
				+ LOGOUT) {
			protected void onPostExecute(String response) {

				if (this.getStatusCode() == 200) {
					goToLogout();

				}

			}

		};
		request.addHeader(Config.API_SESSION_ID, sessionId);
		request.execute();

	}

	/*
	 * this method removes the sessionId from the shared prefs and redirects to
	 * login
	 * 
	 * @author maisaraFarahat
	 */

	@SuppressWarnings("deprecation")
	private void goToLogout() {
		SharedPreferences sessionIDPrefs = this.getSharedPreferences(
				Config.SETTING, MODE_WORLD_READABLE);
		SharedPreferences.Editor prefsEditor = sessionIDPrefs.edit();
		prefsEditor.putString(Config.SESSION_ID, null);

		prefsEditor.commit();

		SharedPreferences prefs = getSharedPreferences(Config.GCM_DATA,
				MODE_PRIVATE);
		prefs.edit().remove(Config.PROPERTY_REG_ID).commit();

		Intent intent = new Intent(getApplicationContext(),
				SplashActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		startActivity(intent);
		this.finish();

	}
}
