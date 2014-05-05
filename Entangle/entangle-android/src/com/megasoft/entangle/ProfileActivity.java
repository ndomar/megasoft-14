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
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.ImageRequest;

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

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);
		ProfileFragment profile = new ProfileFragment();
		Bundle bundle = new Bundle();
		bundle.putInt("tangleId", getIntent().getIntExtra("tangleId", 0));
		bundle.putInt("userId", getIntent().getIntExtra("userId", 0));
		profile.setArguments(bundle);
		FragmentTransaction transaction = getSupportFragmentManager().beginTransaction();
		transaction.add(R.id.profile_layout, profile);
		transaction.commit();
	}


	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.profile, menu);
		return true;
	}
	
}
