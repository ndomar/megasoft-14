package com.megasoft.entangle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.os.Bundle;
import android.app.Activity;
import android.support.v4.app.FragmentTransaction;
import android.content.Intent;
import android.view.Menu;

public class ProfileActivity extends FragmentActivity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);
		Intent intent = getIntent();
		ProfileFragment profile = new ProfileFragment();
		Bundle bundle = new Bundle();
		bundle.putInt("tangleId", intent.getIntExtra("tangleId", 0));
		bundle.putInt("userId", intent.getIntExtra("userId", 0));
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
