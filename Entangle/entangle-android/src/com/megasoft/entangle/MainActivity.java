package com.megasoft.entangle;

import org.apache.http.client.methods.HttpPost;
import com.megasoft.entangle.acceptPendingInvitation.ManagePendingInvitationActivity;
import com.megasoft.requests.PostRequest;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		setContentView(R.layout.activity_main);
		Intent intent = new Intent(this, Request.class);
		startActivity(intent);
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main); 
		startActivity((new Intent(this,ManagePendingInvitationActivity.class)).putExtra("com.megasoft.entangle.tangleId", 2));
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
