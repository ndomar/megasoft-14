package com.megasoft.entangle;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.Button;

public class ProfileActivity extends Activity {
	private Button back;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		Intent profileIntent = getIntent();
		setContentView(R.layout.view_profile);
		back = (Button) findViewById(R.id.button2);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	public void goBackLogin(View view) {
		Intent backLogin = new Intent(this, MainActivity.class);
		startActivity(backLogin);
	}
}
