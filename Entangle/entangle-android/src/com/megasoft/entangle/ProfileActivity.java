package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.ImageRequest;
import com.megasoft.requests.PostRequest;

/**
 * Views a user's profile given his user Id and the tangle Id that redirected to
 * the profile
 * 
 * @author Almgohar
 */

public class ProfileActivity extends FragmentActivity {

	/**
	 * The Button that redirects to the EditProfileActivity
	 */
	private Button edit;

	/**
	 * The button that allows the user to leave the current tangle
	 */
	private Button leave;
	/**
	 * The TextView that holds the user's name
	 */
	private TextView name;

	/**
	 * The TextView that holds the user's description
	 */
	private TextView description;

	/**
	 * The TextView that holds the user's credit/balance
	 */
	private TextView balance;

	/**
	 * The TextView that holds the user's birth date
	 */
	private TextView birthDate;

	/**
	 * The ImageView that indicates whether the user is verified
	 */
	private ImageView verifiedView;

	/**
	 * The ImageView that holds the user's profile picture
	 */
	private ImageView profilePictureView;

	/**
	 * The LinearLayout that holds the user's transactions
	 */
	private LinearLayout transactionsLayout;

	/**
	 * The preferences instance
	 */
	private SharedPreferences settings;

	/**
	 * The id of the logged in user
	 */
	private int loggedInId;

	/**
	 * The tangle Id from which we were redirected
	 */
	private int tangleId;

	/**
	 * The user Id whose profile we want to view
	 */
	private int userId;

	/**
	 * The session Id of the logged in user
	 */

	private String sessionId;

	private static final String LOGOUT = "/user/logout";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);
		ProfileFragment profile = new ProfileFragment();
		Bundle bundle = new Bundle();
		bundle.putInt("tangleId", getIntent().getIntExtra("tangleId", 0));
		bundle.putInt("userId", getIntent().getIntExtra("userId", 0));
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

		Intent intent = new Intent(getApplicationContext(), LoginActivity.class);
		intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		intent.putExtra("EXIT", true);
		startActivity(intent);
		this.finish();

	}

}
