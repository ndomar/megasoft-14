package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.entangle.R.drawable;
import com.megasoft.requests.PostRequest;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.notifications.GCMRegistrationActivity;
import com.megasoft.requests.PostRequest;

@SuppressLint({ "NewApi", "WorldReadableFiles" })
public class LoginActivity extends Activity {
	private EditText username;
	private EditText password;
	public final String LOGIN = "/user/login";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_login);
		username = (EditText) findViewById(R.id.login_username);
		password = (EditText) findViewById(R.id.login_password);

		getActionBar().hide();

		final EditText onTouch = (EditText) findViewById(R.id.login_username);
		username.setOnTouchListener(new View.OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {
				TextView showError = (TextView) findViewById(R.id.invalidUserNameOrPassword);
				showError.setVisibility(View.INVISIBLE);
				username.requestFocus();
				return true;
			}
		});
		final EditText onTouch2 = (EditText) findViewById(R.id.login_username);
		password.setOnTouchListener(new View.OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {
				TextView showError = (TextView) findViewById(R.id.invalidUserNameOrPassword);
				showError.setVisibility(View.INVISIBLE);
				password.requestFocus();
				return true;
			}
		});

		if (getSharedPreferences(Config.SETTING, 0).getString(
				Config.SESSION_ID, null) != null) {
			Intent registerActivity = new Intent(this, HomeActivity.class);
			startActivity(registerActivity);
		}

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

		username = (EditText) findViewById(R.id.login_username);
		password = (EditText) findViewById(R.id.login_password);

		JSONObject json = new JSONObject();
		try {
			json.put("name", username.getText().toString());
			json.put("password", password.getText().toString());
		} catch (JSONException e) {
			e.printStackTrace();
		}
		PostRequest request = new PostRequest(Config.API_BASE_URL_SERVER
				+ LOGIN) {
			protected void onPostExecute(String response) {
				password.setText("");
				if (this.getStatusCode() == 201) {
					goToHome(response);
				} else {

					TextView showError = (TextView) findViewById(R.id.invalidUserNameOrPassword);
					showError.setVisibility(View.VISIBLE);
				}

			}
		};
		request.setBody(json);
		request.execute();

	}

	public void cancel(View view) {
		Intent intent = new Intent(this, SplashActivity.class);
		startActivity(intent);
		this.finish();
	}

	public void clearError(View view) {
		TextView hideError = (TextView) findViewById(R.id.invalidUserNameOrPassword);
		hideError.setVisibility(View.INVISIBLE);

	}

	/*
	 * this method will redirect to the HomeActivity (used by me only)
	 * 
	 * @author maisaraFarahat
	 */
	@SuppressWarnings("deprecation")
	private void goToHome(String response) {

		try {
			JSONObject json = new JSONObject(response);
			SharedPreferences sessionIDPrefs = this.getSharedPreferences(
					Config.SETTING, 0);
			SharedPreferences.Editor prefsEditor = sessionIDPrefs.edit();
			prefsEditor.putString(Config.SESSION_ID,
					json.getString(Config.SESSION_ID));
			prefsEditor.putInt(Config.USER_ID, json.getInt("userId"));
			prefsEditor.putString(Config.PROFILE_IMAGE,
					json.getString("profileImage"));
			prefsEditor.putString(Config.USERNAME,
					json.getString(Config.USERNAME));

			prefsEditor.commit();
		} catch (JSONException e) {
			e.printStackTrace();
		}
		Intent homeActivity = new Intent(this, HomeActivity.class);
		startActivity(homeActivity);

		this.finish();
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

}
