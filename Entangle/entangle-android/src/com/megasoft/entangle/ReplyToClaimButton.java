package com.megasoft.entangle;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;


public class ReplyToClaimButton extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_reply_to_claim_button);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.reply_to_claim_button, menu);
		return true;
	}
		

	public void forward(View view) {
		Intent in = new Intent(this, ReplyToClaimEnterText.class);
		startActivity(in);
	}
		
			
	
			
			
			
			
			
	
}