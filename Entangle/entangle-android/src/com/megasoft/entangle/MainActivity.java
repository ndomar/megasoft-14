package com.megasoft.entangle;

import org.apache.http.client.methods.HttpPost;

import com.megasoft.entangle.acceptPendingInvitation.ManagePendingInvitationActivity;
import com.megasoft.entangle.viewtanglelsit.TangleStreamActivity;
import com.megasoft.requests.PostRequest;

import android.app.Activity;
import android.os.Bundle;
import android.content.Intent;
import android.view.Menu;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
<<<<<<< HEAD
		setContentView(R.layout.activity_main);

		goToOffer();
=======
		setContentView(R.layout.activity_main);  
		startActivity((new Intent(this,TangleStreamActivity.class)));
>>>>>>> 6ad054301d90f9bfda907c33e2150ae6c6ad173a
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
<<<<<<< HEAD
	}

	/**
	 * this navigates to OfferNotify activity
	 * @return None
	 * @author mohamedzayan
	 */
	public void goToOffer() {
		Intent intent = new Intent(this, OfferNotify.class);
		startActivity(intent);
=======
>>>>>>> 6ad054301d90f9bfda907c33e2150ae6c6ad173a
	}

}
