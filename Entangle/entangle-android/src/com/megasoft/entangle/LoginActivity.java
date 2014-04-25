package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

@SuppressLint({ "NewApi", "WorldReadableFiles" })
public class LoginActivity extends Activity {
	private EditText username;
	private EditText password;
	private Button login;
	private Button register;
	public final String LOGIN = "/user/login";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_login);
		username = (EditText) findViewById(R.id.usernameBox);
		password = (EditText) findViewById(R.id.passwordBox);
		login = (Button) findViewById(R.id.loginButton);
		register = (Button) findViewById(R.id.registerButton);

	}

	/*
	 * this method will receive a UserName and a password from the text boxes in
	 * the UI checks for valid credentials and calls go to profile
	 * 
	 * @param View
	 * 
	 * @author maisaraFarahat
	 */

	public void login(View view) {

		username = (EditText) findViewById(R.id.usernameBox);
		password = (EditText) findViewById(R.id.passwordBox);

		JSONObject json = new JSONObject();
		try {
			json.put("username", username);
			json.put("password", password);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		PostRequest request = new PostRequest(Config.API_BASE_URL + LOGIN) {
			protected void onPostExecute(String response) {

				if (this.getStatusCode() == 201) {
					Toast.makeText(getApplicationContext(), "Redirecting...",
							Toast.LENGTH_SHORT).show();

					goToHome(response);

				} else if (this.getStatusCode() == 400) {
					Toast.makeText(getApplicationContext(),
							"Wrong Credentials", Toast.LENGTH_SHORT).show();
				}

			}
		};
		request.setBody(json);
		request.execute();

	}

	/*
	 * this method will redirect to the HomeActivity (used by me only)
	 * 
	 * @author maisaraFarahat
	 */
	@SuppressWarnings("deprecation")
	private void goToHome(String response) {

		SharedPreferences sessionIDPrefs = this.getSharedPreferences(
				Config.SETTING, MODE_WORLD_READABLE);
		SharedPreferences.Editor prefsEditor = sessionIDPrefs.edit();
		prefsEditor.putString(Config.SESSION_ID, response);
		prefsEditor.commit();

		Intent homeActivity = new Intent(this, HomeActivity.class);
		homeActivity.putExtra("sessionId", response);
		startActivity(homeActivity);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {

		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}

	public void register(View view) {
		Intent registerActivity = new Intent(this, HomeActivity.class);
		startActivity(registerActivity);
	}
}
