package com.megasoft.entangle;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		// setContentView(R.layout.activity_main);
		startActivity((new Intent(this, EditProfileActivity.class)));
		// .putExtra("com.megasoft.entangle.tangleId", 2));

		// setContentView(R.layout.template_create_offer);
		// getActionBar().hide();


	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		// getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	public void moveToRegistration(View view) {
		Intent intent = new Intent(this, CreateTangleActivity.class);
		startActivity(intent);
	}

}
