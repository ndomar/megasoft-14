package com.megasoft.entangle;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Typeface;
import android.os.Bundle;
import android.text.SpannableString;
import android.text.Spanned;
import android.text.method.LinkMovementMethod;
import android.text.style.ClickableSpan;
import android.text.style.StyleSpan;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.requests.PostRequest;

@SuppressLint({ "NewApi", "WorldReadableFiles" })
public class MainActivity extends Activity {
	private EditText username;
	private EditText password;
	private Button login;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		username = (EditText) findViewById(R.id.usernameBox);
		password = (EditText) findViewById(R.id.passwordBox);
		login = (Button) findViewById(R.id.button1);

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
		PostRequest request = new PostRequest("http://entangle/user/login") {
			protected void onPostExecute(String response) {

				if (this.getStatusCode() == 201) {
					Toast.makeText(getApplicationContext(), "Redirecting...",
							Toast.LENGTH_SHORT).show();
					// adding the session id to the shared preferences

					goToHome(response);
				} else if (this.getStatusCode() == 400) {
					Toast.makeText(getApplicationContext(),
							"Wrong Credentials", Toast.LENGTH_SHORT).show();

				}
			}
		};
		request.setBody(json);
		request.execute();

		Toast.makeText(getApplicationContext(), "Wrong Credentials",
				Toast.LENGTH_SHORT).show();

	}

	/*
	 * this method will be called when clicking " not registered yet " and calls
	 * goToRegister()
	 * 
	 * @author maisaraFarahat
	 */
	private void register() {
		SpannableString ss = new SpannableString("Not registered yet?");
		ss.setSpan(new StyleSpan(Typeface.ITALIC), 22, 27,
				Spanned.SPAN_EXCLUSIVE_EXCLUSIVE);
		ClickableSpan clickableSpan = new ClickableSpan() {
			@Override
			public void onClick(View textView) {
				startActivity(new Intent(MainActivity.this,
						RegisterActivity.class));
			}
		};
		ss.setSpan(clickableSpan, 22, 27, Spanned.SPAN_EXCLUSIVE_EXCLUSIVE);

		TextView textView = (TextView) findViewById(R.id.register);
		textView.setText(ss);
		textView.setMovementMethod(LinkMovementMethod.getInstance());

		goToRegister();
	}

	/*
	 * this method will redirect to the RegisterActivity (used by me only)
	 * 
	 * @author maisaraFarahat
	 */
	private void goToRegister() {
		Intent RegisterActivity = new Intent(this, RegisterActivity.class);
		startActivity(RegisterActivity);
	}

	/*
	 * this method will redirect to the HomeActivity (used by me only)
	 * 
	 * @author maisaraFarahat
	 */
	@SuppressWarnings("deprecation")
	private void goToHome(String response) {

		SharedPreferences sessionIDPrefs = this.getSharedPreferences(
				"sessionIDPrefs", MODE_WORLD_READABLE);
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

}