package com.megasoft.entangle;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;

import com.megasoft.entangle.acceptPendingInvitation.ManagePendingInvitationActivity;

public class MainActivity extends Activity {

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		startActivity(new Intent(getApplicationContext(),
				GCMRegistrationActivity.class));
		// setContentView(R.layout.activity_main);
		// startActivity((new
		// Intent(this,ManagePendingInvitationActivity.class)).putExtra("com.megasoft.entangle.tangleId",
		// 2));
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
