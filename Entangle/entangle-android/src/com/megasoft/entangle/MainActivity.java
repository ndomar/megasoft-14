package com.megasoft.entangle;

import android.R;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_list_item);
		Intent intent = new Intent(getApplicationContext(),
				GCMRegistrationActivity.class);
		startActivity(intent);
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
