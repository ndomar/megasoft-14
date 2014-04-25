package com.megasoft.entangle;

import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.view.Menu;

/**
 * Views a user's profile given his user Id and the tangle Id that redirected to
 * the profile
 * 
 * @author Almgohar
 */

public class ProfileActivity extends FragmentActivity {

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
