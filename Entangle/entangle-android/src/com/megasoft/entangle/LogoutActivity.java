package com.megasoft.entangle;

import android.annotation.SuppressLint;
import android.app.Activity;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

@SuppressLint("WorldReadableFiles")
public class LogoutActivity extends Activity {
	private Button logout;
	public final String LOGOUT = "/user/logout";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_logout);
		logout = (Button) findViewById(R.id.logoutButton);

	}

	/*
	 * this method gets the sessionId and sends a request to the server and then
	 * calls goToLogout method
	 * 
	 * @param View
	 * 
	 * @author maisaraFarahat
	 */

	@SuppressWarnings("deprecation")
	public void logout(View view) {

		SharedPreferences myPrefs = this.getSharedPreferences(Config.SETTING,
				MODE_WORLD_READABLE);
		String sessionId = myPrefs.getString(Config.SESSION_ID, "");

		PostRequest request = new PostRequest(Config.API_BASE_URL_SERVER
				+ LOGOUT) {
			protected void onPostExecute(String response) {

				if (this.getStatusCode() == 200) {
					Toast.makeText(getApplicationContext(), "Logging out...",
							Toast.LENGTH_SHORT).show();

					goToLogout();

				} else {
					Toast.makeText(getApplicationContext(),
							"something wrong happened", Toast.LENGTH_SHORT)
							.show();
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
				"sessionIDPrefs", MODE_WORLD_READABLE);
		SharedPreferences.Editor prefsEditor = sessionIDPrefs.edit();
		prefsEditor.putString(Config.SESSION_ID, null);
		prefsEditor.remove(Config.PROPERTY_REG_ID);
		prefsEditor.commit();

		Intent homeActivity = new Intent(this, MainActivity.class);
		startActivity(homeActivity);

	}

}
