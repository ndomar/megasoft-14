package com.megasoft.entangle;

import android.app.Activity;
import android.os.Bundle;
import android.content.Intent;
import android.view.Menu;
import android.view.View;


public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		startActivity((new Intent(this,InviteUserActivity.class)).putExtra("com.megasoft.entangle.tangleId", 2));
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true; 
	}
	
	public void Switch(View v) {
		Intent i = new Intent(MainActivity.this , Notification.class);
		startActivity(i);
	}

}
