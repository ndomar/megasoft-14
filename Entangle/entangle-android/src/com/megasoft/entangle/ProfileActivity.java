package com.megasoft.entangle;

import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;

public class ProfileActivity extends FragmentActivity {

	private int userId;
	private int tangleId;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);
		ProfileSuperFragment profile = new ProfileSuperFragment();
		Bundle bundle = new Bundle();
		this.tangleId = getIntent().getIntExtra("tangleId", -1);
		this.userId = getIntent().getIntExtra("userId", -1);
		bundle.putInt("tangleId", tangleId);
		bundle.putInt("userId", userId);
		bundle.putBoolean("general", false);
		profile.setArguments(bundle);
		FragmentTransaction transaction = getSupportFragmentManager()
				.beginTransaction();
		transaction.add(R.id.profile, profile);
		transaction.commit();
	}
}
