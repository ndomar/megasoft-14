package com.megasoft.entangle;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;

/**
 * The fragment that adds a new email field with it's remove button
 * @author MohamedBassem
 *
 */
public class EmailEntryFragment extends Fragment {
	/**
	 * The edit text in this entry.
	 */
	private EditText editText;
	
	/**
	 * The button that removes the mail field
	 */
	private Button removeButton;
	
	/**
	 * The inflated view of the fragment
	 */
	public View view;
	
	/**
	 * The parent activity
	 */
	private AddEmailInterface activity;
	
	/**
	 * The event listener to the email field change to add a new field whenever someone
	 * types in the last field.
	 * 
	 * @author MohamedBassem
	 */
	TextWatcher watcher = new TextWatcher() {
		
		@Override
		public void onTextChanged(CharSequence s, int start, int before, int count) {}
		
		@Override
		public void beforeTextChanged(CharSequence s, int start, int count,
				int after) {}
		
		@Override
		public void afterTextChanged(Editable s) {
			editText.setError(null);
			if(s.length() >= 1){
				editText.removeTextChangedListener(this);
				activity.addEmailField();
			}
		}
	};
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		this.view = inflater.inflate(R.layout.fragment_invite_user_email_field, container,false);
		this.editText = (EditText) view.findViewById(R.id.invite_user_edit_text);
		this.removeButton = (Button) view.findViewById(R.id.invite_user_remove_edit_text);
		setlisteners();
		setTextChangeListener();
		return view;
	}
	
	/**
	 * Sets the text change listener for the edit text.
	 */
	public void setTextChangeListener() {
		editText.addTextChangedListener(watcher);
	}
	
	/**
	 * Sets the remove button onClick listener
	 */
	private void setlisteners() {
		
		removeButton.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				activity.removeEmailField(EmailEntryFragment.this);
			}
		});

	}
	
	/**
	 * @return the email written in the edit text.
	 */
	public String getEmail(){
		return this.editText.getText().toString();
	}
	
	/**
	 * @return The edit text in this layout
	 */
	public EditText getEditText(){
		return this.editText;
	}
	
	/**
	 * Sets the parent activity
	 */
	public void setActivity(AddEmailInterface activity){
		this.activity = activity;
	}
}
