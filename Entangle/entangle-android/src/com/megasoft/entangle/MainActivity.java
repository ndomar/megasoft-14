package com.megasoft.entangle;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;

public class MainActivity extends Activity {
	private Button login;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		login = (Button) findViewById(R.id.loginRedirectButton);

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

		Intent loginActivity = new Intent(this, LoginActivity.class);
		startActivity(loginActivity);

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
