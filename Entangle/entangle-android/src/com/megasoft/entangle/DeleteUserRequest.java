package com.megasoft.entangle;

import android.R;
import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.ActivityCompat;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;

public class DeleteUserRequest extends ActivityCompat {

	
	String tag = "hello";
	 @Override
	  public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);

	    // Assuming you are using xml layout
	   Button button = (Button)findViewById(R.id.button1);

	    button.setOnClickListener(new OnClickListener() {
	    	
	    	
	      public void onClick(View arg0) {
	        Intent viewIntent =
	          new Intent("android.intent.action.VIEW",
	            Uri.parse("http://www.stackoverflow.com/"));
	          startActivity(viewIntent);
	      }
	    });

	  }

	
}
