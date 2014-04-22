package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

public class LoginActivity extends Activity {
	private EditText username;
	private EditText password;
	private Button login;
	private Button register;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_login);
		username = (EditText) findViewById(R.id.usernameBox);
		password = (EditText) findViewById(R.id.passwordBox);
		login = (Button) findViewById(R.id.loginButton);
		register = (Button) findViewById(R.id.registerButton);

		register.setOnClickListener(new OnClickListener() {

			public void onClick(View v) {

				Uri uri = Uri.parse("http://entangle.io/register");
				Intent intent = new Intent(Intent.ACTION_VIEW, uri);
				startActivity(intent);

			}

		});
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
			json.put("username", username.getText().toString());
			json.put("password", password.getText().toString());
		} catch (JSONException e) {
			e.printStackTrace();
		}
		PostRequest request = new PostRequest(Config.API_BASE_URL+ "/login") {
			protected void onPostExecute(String response) {

				if (this.getStatusCode() == 201) {
					goToHome(response);
				} else if (this.getStatusCode() == 400) {
					Log.e("test", this.getStatusCode() + "");
					Log.e("test", this.getErrorMessage());
					Toast.makeText(getApplicationContext(),
							"Wrong Credentials", Toast.LENGTH_SHORT).show();
				} else {
					Log.e("test", this.getStatusCode() + "");
					Log.e("test", this.getErrorMessage());
					Toast.makeText(getApplicationContext(), "Didn't merge the API yet",
							Toast.LENGTH_SHORT).show();

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
	private void goToHome(String response) {
		
		try {
			JSONObject x = new JSONObject(response);
			String sessionId = x.getString("sessionId");
			int userId = x.getInt("userId");
			SharedPreferences sessionIDPrefs = this.getSharedPreferences(
					Config.SETTING, 0);
			SharedPreferences.Editor prefsEditor = sessionIDPrefs.edit();
			prefsEditor.putString(Config.SESSION_ID, sessionId);
			prefsEditor.putInt(Config.USER_ID, userId); 
			prefsEditor.commit();

			Intent homeActivity = new Intent(this, HomeActivity.class);
			startActivity(homeActivity);
		} catch (JSONException e) {
			e.printStackTrace();
		}
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