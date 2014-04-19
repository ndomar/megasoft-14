package com.megasoft.entangle;

import org.w3c.dom.Text;

import android.os.Bundle;
import android.app.Activity;
import android.text.Editable;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

public class ReplyToClaim extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_reply_to_claim);
		
		
	
		final EditText reptxt = (EditText)findViewById(R.id.txtreply);
		final EditText subj = (EditText)findViewById(R.id.Subject);
		final EditText recv = (EditText)findViewById(R.id.receiver);
		
		Button repbtn = (Button)findViewById(R.id.btnreply);
		
		//values passed by the claim activity
		String subjectM = null ;
		String receiverM = null;
		
		subj.setText(subjectM);
		recv.setText(receiverM);
	
	
	
	
	repbtn.setOnClickListener(new View.OnClickListener() {
		
		@Override
		public void onClick(View v) 
		{
			
		String msg = reptxt.getText().toString() ;
		
				
			
			
		}
	});
	
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.reply_to_claim, menu);
		return true;
	}

}
