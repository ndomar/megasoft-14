package com.megasoft.entangle;

import android.annotation.SuppressLint;
import android.app.DialogFragment;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;

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
		AutoCompleteTextView tag = (AutoCompleteTextView) view
				.findViewById(R.id.tagValue);
		tag.setAdapter(new ArrayAdapter<String>(getActivity(),
				android.R.layout.simple_list_item_1,
				((TangleProfilePage) getActivity()).getTagsSuggestions()));
	}

	private void setUserSuggestions(View view) {
		AutoCompleteTextView user = (AutoCompleteTextView) view
				.findViewById(R.id.tagValue);
		user.setAdapter(new ArrayAdapter<String>(getActivity(),
				android.R.layout.simple_list_item_1,
				((TangleProfilePage) getActivity()).getUsersSuggestions()));
	}

	private void setButtonsActions(View view) {

	}
}
