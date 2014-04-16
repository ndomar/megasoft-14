package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;

public class ReplyToClaimEnterText extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_reply_to_claim_enter_text);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.reply_to_claim_enter_text, menu);
		return true;
	}

}
