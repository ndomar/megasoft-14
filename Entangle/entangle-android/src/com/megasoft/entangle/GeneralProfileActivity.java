package com.megasoft.entangle;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.Toast;

@SuppressLint("WorldReadableFiles")
public class GeneralProfileActivity extends Activity {

	private static final String LOGOUT = "/user/logout";

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

		Intent intent = new Intent(getApplicationContext(),
				LoginActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		intent.putExtra("EXIT", true);
		startActivity(intent);
		this.finish();

	}

}
