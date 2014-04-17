package com.megasoft.entangle;

import android.annotation.SuppressLint;
import android.app.DialogFragment;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

@SuppressLint("NewApi")
public class FilteringFragment extends DialogFragment {

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		View view = inflater.inflate(R.layout.filtering_fragment, container,
				false);
		setTagSuggestions(view);
		setUserSuggestions(view);
		setButtonsActions(view);
		return view;
	}

	private void setTagSuggestions(View view) {

	}

	private void setUserSuggestions(View view) {

	}

	private void setButtonsActions(View view) {

	}
}
